<?
use Phalcon\Tools\Cli;
namespace Phalcon\Builder;

class Js extends \Phalcon\DI\Injectable
{

    public function __construct($controller, $actions){
        $target = $this->config->application->publicDir.'js/'.(!defined('MODULE')?'':'modules/');
        $basePath = TEMPLATE_PATH.'/js/'.(!defined('MODULE')?'':'modules/');
        $ext = !defined('MODULE')?'js':'mjs';
        $actions = explode(',', $actions);
        if($controller === 'helper'){
            $source = $basePath.'helper.'.$ext;
            if(count($actions) === 0){
                Cli::error("helper must have a name");
            } else {
                foreach($actions as $action){
                    $this->create($source, $target, [
                        $action,
                        ucfirst($action),
                        'helper/',
                        APP
                    ]);
                }
            }
        } else {
            foreach(glob($basePath.'ma*.'.$ext) as $source){            
                foreach($actions as $action){
                    if($action !== ""){
                        $this->create($source, $target, [
                            $action.ucfirst($controller),
                            ucfirst($action).ucfirst($controller),
                            $controller.'/'.$action.'/',
                            APP
                        ]);
                    } else {
                        $this->create($source, $target, [
                            $controller,
                            ucfirst($controller),
                            $controller.'/',
                            APP
                        ]);
                    }
                }
            }
        }
    }

    private function create($source, $target, $params){
        $content = file_get_contents($source);
        $content = str_replace(['[name]', '[className]', '[path]', '[app]'], $params, $content);
        if(defined('MODULE') && substr_count($params[2], '/') === 2){
            $content = str_replace('../../../../', '../../../../../', $content);
        }
        $target .= $params[2];
        exec("mkdir -p $target");
        $name = basename($source);
        $target .= strpos($name, 'helper.')===0 ? str_replace('helper', $params[0], $name):$name;
        if(!file_exists($target)){
            file_put_contents($target, $content);
            echo $target."\n";
        }
    }
}