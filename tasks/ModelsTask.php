<?
use Phalcon\Tools\Cli,
Phalcon\Builder\Model,
\Phalcon\CLI\Task;
/**
 * Task managing models
 */
class ModelsTask extends Task
{
    /**
     * Main task action (not implemented)
     */
    public function mainAction():void {

    }

    /**
     * Generate all models from database
     * 
     * @return void
     */
    public function generateAction():void{
        exec('rm -rf '.$this->config->application->modelsDir.'*');
        if($this->config->application->modelsDir === ''){
            Cli::error('modelsDir path is empty');
        }
        $constraints = [];
        $tables = $this->db->listTables($this->config[ENV]->database->dbname);
        if(count($tables) === 0){ // no table in bdd, load the default
            $query = file_get_contents($this->config->application->rootDir.'defaultModels.sql');
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