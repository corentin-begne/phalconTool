<?

namespace Phalcon\Builder;
use Phalcon\DI,
Phalcon\Tools\Cli,
Phalcon\Db;

class Migration extends \Phalcon\Mvc\User\Component
{
    public function __construct(){
        $modelVersion = '<? 
    class MigrationVersion[nb]{
        public function up(){
            return [up];
        }
        public function down(){
            return [down];
        }
    }
?>';        
        $definitions = ['tables'=>[], 'fields'=>[]];
        $modelActionTpl = ['up'=>$definitions, 'down'=>$definitions];
        $migration = [];
        // generate migration difference between models and bdd
        $models = glob($this->config->application->modelsDir.'*.php');
        if(count($models) === 0){
            Cli::error('No models to check');
        }
        foreach($models as $model){
            $model = basename($model, '.php');
            $sourceModel = new $model(); 
            $sourceModel = $sourceModel->getSource();
            if(!$this->db->tableExists($sourceModel)){
                $modelActionTpl['up']['tables'][] = $sourceModel;
                $modelActionTpl['down']['tables'][] = $sourceModel;
            }
            // check fields, get annotations     
            foreach($model::getColumnsDescription() as $field => &$fieldAnnotation){
                $field = substr($field, strpos($field, '_')+1);                                  
                $fieldDesc = $this->db->fetchOne('show columns from '.$sourceModel.' where Field = \''.$field .'\'', Db::FETCH_ASSOC);
                if(!$fieldDesc){
                    if(!isset($modelActionTpl['up']['fields']['add'])){
                        $modelActionTpl['up']['fields']['add'] = [];
                    }
                    if(!isset($modelActionTpl['down']['fields']['drop'])){
                        $modelActionTpl['down']['fields']['drop'] = [];
                    }
                    if(!isset($modelActionTpl['up']['fields']['add'][$sourceModel])){
                        $modelActionTpl['up']['fields']['add'][$sourceModel] = [];
                    }
                    if(!isset($modelActionTpl['down']['fields']['drop'][$sourceModel])){
                        $modelActionTpl['down']['fields']['drop'][$sourceModel] = [];
                    }
                    $modelActionTpl['up']['fields']['add'][$sourceModel][$field] = $fieldAnnotation;
                    $modelActionTpl['down']['fields']['drop'][$sourceModel][] = $field;
                } else if(!$this->checkField($fieldAnnotation, $fieldDesc)){
                    if(!isset($modelActionTpl['up']['fields']['modify'])){
                        $modelActionTpl['up']['fields']['modify'] = [];
                    }
                    if(!isset($modelActionTpl['down']['fields']['modify'])){
                        $modelActionTpl['down']['fields']['modify'] = [];
                    }
                    if(!isset($modelActionTpl['up']['fields']['modify'][$sourceModel])){
                        $modelActionTpl['up']['fields']['modify'][$sourceModel] = [];
                    }
                    if(!isset($modelActionTpl['down']['fields']['modify'][$sourceModel])){
                        $modelActionTpl['down']['fields']['modify'][$sourceModel] = [];
                    }
                    $modelActionTpl['up']['fields']['modify'][$sourceModel][$field] = $fieldAnnotation;
                    $modelActionTpl['down']['fields']['modify'][$sourceModel][$field] = $fieldDesc;
                }
                
            }
        }
        $migrations = glob($this->config->application->migrationsDir.'*.php');
        if(self::getCurrentVersion()<count($migrations)){
            Cli::error('There\'s already a migration to run');
        } else if(count($modelActionTpl['up']['fields']) === 0 && count($modelActionTpl['up']['tables']) === 0){
            Cli::error('No migration to create');
        } else {
            $nb = (count($migrations)+1);
            $up = var_export($modelActionTpl['up'], true);
            $down = var_export($modelActionTpl['down'], true);
            file_put_contents($this->config->application->migrationsDir.'MigrationVersion'.$nb.'.php', str_replace([
                '[nb]',
                '[up]',
                '[down]',
            ], [
                $nb,
                $up,
                $down
            ], $modelVersion));
        }        
    }

    public function normalize($field){
        $type = $field['Type'];
        if(strpos($field['Type'], '(') !== false){
            $type = substr($field['Type'], 0, strpos($field['Type'], '('));
            $length = substr($field['Type'], strpos($field['Type'], '(')+1);
            $length = substr($length, 0, strpos($length, ')'));
            if(strpos($length, ',') !== false){
                $length = substr($length, 0, strpos($length, ','));
            }
        }
        $data = [
            'type' => $type,
            'isNull' => ($field['Null'] === 'NO') ? false : true,
        ];
        if(!empty($field['Default'])){
            $data['default'] = $field['Default'];
        }
        if(!empty($field['Extra'])){
            $data['extra'] = $field['Extra'];
        }
        if(!empty($field['Key'])){
            $data['key'] = $field['Key'];
        }
        if(isset($length)){
            $data['length'] = $length;
        }
        return $data;
    }

    public function checkField($new, &$old){
        $old = $this->normalize($old);
        if(count(array_diff($new ,$old))>0){
            return false;
        }
        return true;
    }

    public static function getCurrentVersion(){
        $file = DI::getDefault()->getConfig()->application->migrationsDir.ENV.'_version';
        if(!file_exists($file)){
            self::setCurrentVersion(0);
        }
        return (int)file_get_contents($file);
    }

    public static function setCurrentVersion($version){
        return file_put_contents(DI::getDefault()->getConfig()->application->migrationsDir.ENV.'_version', $version);
    }

}