<?
use Phalcon\Builder\Model,
Phalcon\Builder\Controller,
Phalcon\Builder\Js,
Phalcon\Builder\Css,
Phalcon\Builder\Less,
Phalcon\Text as Utils;
class GenerateTask extends \Phalcon\CLI\Task
{
    public function mainAction() {

    }

    /**
     * generate all models
     */
    public function modelsAction() {
        $constraints = [];
        foreach($this->db->listTables($this->config[ENV]->database->dbname) as &$table){
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

    /**
     * generate module, action, css/js, rest, security
     * @params([params: [], options: []])
     */
    public function controllerAction($params){
        list($controller, $actions) = $params;
        new Controller($controller, $actions);
    }

    public function jsAction($params){
        list($controller, $actions) = $params;
        new Js($controller, $actions);
    }

    public function cssAction($params){
        list($controller, $actions) = $params;
        new Css($controller, $actions);
    }

    public function lessAction($params){
        list($controller, $action) = $params;
        new Less($controller, $action);
    }
}