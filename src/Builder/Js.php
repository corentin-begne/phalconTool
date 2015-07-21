<?

namespace Phalcon\Builder;

class Js extends \Phalcon\Mvc\User\Component
{
    public function __construct($controller, $actions){
        foreach(glob(TEMPLATE_PATH.'/js/*.js') as $source){
            $target = $this->config->application->publicDir.'js/';
            if(isset($actions)){
                foreach(explode(',', $actions) as $action){
                    $this->create($source, $target, [
                        $action.ucfirst($controller),
                        ucfirst($action).ucfirst($controller),
                        $controller.'/'.$action.'/'
                    ]);
                }
            } else{
                $this->create($source, $target, [
                    $controller,
                    ucfirst($controller),
                    $controller.'/'
                ]);
            }
        }
    }

    private function create($source, $target, $params){
        $content = file_get_contents($source);
        $content = str_replace(['[name]', '[className]', '[path]'], $params, $content);
        $target .= $params[2];
        exec("mkdir -p $target");
        $target .= basename($source);
        if(!file_exists($target)){
            file_put_contents($target, $content);
            echo $target."\n";
        }
    }
}