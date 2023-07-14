<?

namespace Phalcon\Builder;

use Phalcon\Support\Helper\Str\Camelize,
Phalcon\Support\Helper\Str\Uncamelize,
Phalcon\DI\Injectable;

/**
 * Manage tasks generation
 */
class Task extends Injectable
{
    /**
     * Object to camelize texts
     * @var \Phalcon\Support\Helper\Str\Camelize
     */
    private Camelize $camelize;
    /**
     * Object to uncamelize texts
     * @var \Phalcon\Support\Helper\Str\Uncamelize
     */
    private Uncamelize $uncamelize;

    /**
     * Generate task
     * 
     * @param string $task Task Name
     * @param null|string $action='' Actions list separated by a comma
     */
    public function __construct(string $task, null|string $actions=''){        
        $this->camelize = new Camelize();
        $this->uncamelize = new Uncamelize();
        $source = file_get_contents(TEMPLATE_PATH.'/php/task.php');
        $name = ($this->camelize)(($this->uncamelize)($task));
        $target = $this->config->application->tasksDir.$name.'Task.php';
        if(file_exists($target)){
            $source = file_get_contents($target);
        } else {
            $source = str_replace('[name]', $name, $source);
        }        
        if(!empty($actions)){
            $this->setActions($actions, $source);
        }
        file_put_contents($target, $source);        
        echo $target."\n";
    }

    /**
     * Set task actions
     * 
     * @param string $action Actions list separated by a comma
     * @param string $source Task content
     * 
     * @return void
     */
    public function setActions(string $actions, string &$source):void{
        $modelAction = "\tpublic function [name]Action(){\n\n\t}\n\n";     
        $content = '';
        foreach(explode(',', $actions) as $action){
            $content .= str_replace('[name]', lcfirst(($this->camelize)(($this->uncamelize)($action))),$modelAction);               
        }
        $source = str_replace("}\n}", "}\n\n".$content.'}', $source);
    }
}