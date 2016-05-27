<?

namespace Phalcon\Builder;
use Phalcon\DI,
Phalcon\Tools\Cli,
Phalcon\Text as Utils,
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
        $definitions = [
            'tables'=>[
                'create'=>[], 
                'drop'=>[]
            ], 
            'fields'=>[
                'add'=>[],
                'drop'=>[],
                'modify'=>[]
            ], 
            'indexes'=>[
                'add'=>[],
                'drop'=>[]
            ], 
            'uniques'=>[
                'add'=>[],
                'drop'=>[]
            ], 
            'keys'=>[ 
                'foreign'=>[
                    'add'=>[],
                    'drop'=>[]
                ],
                'primary'=>[
                    'add'=>[],
                    'drop'=>[]
                ]
            ]
        ];
        $modelActionTpl = ['up'=>$definitions, 'down'=>$definitions];
        $migration = [];
        // generate migration difference between models and bdd
        $models = glob($this->config->application->modelsDir.'*.php');
        if(count($models) === 0){
            Cli::error('No models to check');
        }
        foreach($this->db->listTables($this->config[ENV]->database->dbname) as $table){
            $sourceModel = Utils::camelize(Utils::uncamelize($table));
            if(!class_exists("\\$sourceModel")){                
                $modelActionTpl['up']['tables']['drop'][] = $table;
                $modelActionTpl['down']['tables']['create'][] = $table;
                // get all fields for the rollback
                $modelActionTpl['down']['fields']['add'][$table] = [];
                foreach($this->db->fetchAll('show columns from '.$table, Db::FETCH_ASSOC) as $field){
                    $modelActionTpl['down']['fields']['add'][$table][$field['Field']] = $this->normalize($field);
                }
            }
        }
        foreach($models as $model){
            $model = basename($model, '.php');
            $sourceModel = new $model(); 
            $sourceModel = $sourceModel->getSource();
            if(!$this->db->tableExists($sourceModel)){
                $modelActionTpl['up']['tables']['create'][] = $sourceModel;
                $modelActionTpl['down']['tables']['drop'][] = $sourceModel;
            }
            $modelActionTpl['up']['fields']['add'][$sourceModel] = [];
            $modelActionTpl['up']['fields']['drop'][$sourceModel] = [];
            $modelActionTpl['up']['fields']['modify'][$sourceModel] = [];
            if(!isset($modelActionTpl['down']['fields']['add'][$sourceModel])){
                $modelActionTpl['down']['fields']['add'][$sourceModel] = [];
            }
            $modelActionTpl['down']['fields']['drop'][$sourceModel] = [];
            $modelActionTpl['down']['fields']['modify'][$sourceModel] = [];
            // check fields, get annotations     
            $fields = $model::getColumnsDescription();
            foreach($fields as $field => &$fieldAnnotation){

                $field = substr($field, strpos($field, '_')+1);                                  
                $fieldDesc = $this->db->fetchOne('show columns from '.$sourceModel.' where Field = \''.$field .'\'', Db::FETCH_ASSOC);
                if(!$fieldDesc){
                    $modelActionTpl['up']['fields']['add'][$sourceModel][$field] = $fieldAnnotation;
                    $modelActionTpl['down']['fields']['drop'][$sourceModel][] = $field;
                    // check relation to add
                  /*  if(isset($fieldAnnotation['key'])){
                        $info = $this->getKeyInfo($field, $fieldAnnotation['key']);
                    }*/
                } else if(!$this->checkField($fieldAnnotation, $fieldDesc)){             
                    $modelActionTpl['up']['fields']['modify'][$sourceModel][$field] = $fieldAnnotation;
                    $modelActionTpl['down']['fields']['modify'][$sourceModel][$field] = $fieldDesc;                    
                }
                
            }
            // now need to check the inverse to drop which are removed
            foreach($this->db->fetchAll('show columns from '.$sourceModel, Db::FETCH_ASSOC) as $fieldDesc){

                if(!isset($fields[$this->getPrefix($sourceModel).'_'.$fieldDesc['Field']])){
                    $modelActionTpl['up']['fields']['drop'][$sourceModel] = $fieldDesc['Field'];
                    $modelActionTpl['down']['fields']['add'][$sourceModel][$fieldDesc['Field']] = $this->normalize($fieldDesc);
                }
            }            
        }        
        $migrations = glob($this->config->application->migrationsDir.'*.php');
        if(self::getCurrentVersion()<count($migrations)){
            Cli::error('There\'s already a migration to run');
        } else if($this->isArrayEmpty($modelActionTpl)){
            Cli::error('No migration to generate');
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

    private function isArrayEmpty($data){
        if(is_array($data)){
        foreach($data as $value){
                if(!$this->isArrayEmpty($value)) {
                    return false;
                }
            }
        } else if(!empty($data)){
            return false;
        }

        return true;
    }

    private function getPrefix($table){
        $prefix = '';
        foreach(explode('_', Utils::uncamelize($table)) as $name){
            $prefix .= $name[0].$name[1];
        }
        return $prefix;
    }

    public function getKeyInfo($field, $key){

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