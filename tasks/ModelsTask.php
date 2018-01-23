<?
use Phalcon\Tools\Cli;
class ModelsTask extends \Phalcon\CLI\Task
{
    public function mainAction($params) {

    }

    public function addAction($params){

    }

    public function modifyAction($params){

    }

    public function dropAction($params){

    }

    public function importAction($params){

    }

    public function exportAction($params){

    }

    /**
     * generate all models
     */
    public function generateAction() {
        exec('rm -rf '.$this->config->application->modelsDir.'*');
        $constraints = [];
        $tables = $this->db->listTables($this->config[ENV]->database->dbname);
        if(count($tables) === 0){ // no table in bdd, load the default
            $query = file_get_contents(TEMPLATE_PATH.'/project/defaultModels.sql');
            $this->db->execute($query);
            $tables = $this->db->listTables($this->config[ENV]->database->dbname);
        }
        foreach($tables as &$table){
            $model = new Model($table);           
            if(isset($model->constraints)){
                foreach($model->constraints as $name => $lines){
                    if(!isset($constraints[$name])){
                        $constraints[$name] = [];
                    }
                    $constraints[$name] = array_merge($constraints[$name], $lines);                           
                }
            }
        }     
        foreach($constraints as $name => $lines){
            $target = $this->config->application->modelsDir.$name.'.php';
            $content = file_get_contents($target);
            $content = str_replace("initialize()\n    {", "initialize()\n    {\n".implode($lines), $content);
            file_put_contents($target, $content);
        }
    }    
}