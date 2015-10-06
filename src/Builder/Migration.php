<?

namespace Phalcon\Builder;
use Phalcon\DI,
Phalcon\Tools\Cli,
Phalcon\Db;

class Migration extends \Phalcon\Mvc\User\Component
{
    public function __construct(){
        $definitions = ['fields'=>[], 'indexes'=>[], 'options'=>[]];
        $modelActionTpl = ['up'=>$definitions, 'down'=>$definitions];
        $migration = [];
        // generate migration difference between models and bdd
        $models = glob($this->config->application->modelsDir.'*.php');
        if(count($models) === 0){
            Cli::error('No models to check');
        }
        foreach($models as $model){
            $model = basename($model, '.php');
            if(!$this->db->tableExists($sourceModel, $this->config[ENV]->database->dbname)){ // need create

            } else {
                // check fields, get annotations     
                foreach($model::getColumnsDescription() as $field => &$fieldAnnotation){
                    $field = substr($field, strpos($field, '_')+1);
                    $sourceModel = new $model(); 
                    $sourceModel = $sourceModel->getSource();
                    $fieldDesc = $this->db->fetchOne('show columns from '.$sourceModel.' where Field = \''.$field .'\'', Db::FETCH_ASSOC);
                    var_dump($fieldDesc, $fieldAnnotation); die;
                }
            }
        }
    }

    public static function getCurrentVersion(){
        return (int)file_get_contents($this->config->application->migrationsDir.'.version');
    }

    public static function setCurrentVersion($version){
        return file_put_contents($this->config->application->migrationsDir.'.version', $version);
    }

}