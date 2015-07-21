<?

namespace Phalcon\Builder;

class Css extends \Phalcon\Mvc\User\Component
{
    public function __construct($controller, $actions){
        $target = $this->config->application->publicDir.'css/';
        if(isset($actions)){
            foreach(explode(',', $actions) as $action){
                $this->create($target, $controller , $action);
            }
        } else{
            $this->create($target, $controller);
        }
    }

    private function create($target, $controller, $action){
        $target .= $controller.'/';
        $target .= isset($action) ? $action.'/' : '';
        exec("mkdir -p $target");
        foreach(glob(TEMPLATE_PATH.'/css/*.less') as $source){
            $file = $target.basename($source);
            if(!file_exists($file)){
                $content = file_get_contents($source);
                if(isset($action) && basename($source) === 'main.less'){
                    $content = str_replace('"../', '"../../', $content);
                }
                file_put_contents($file, $content);
                echo $file."\n";
            }
        }
        new Less($controller, $action);
    }
}