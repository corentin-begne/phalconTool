<?
namespace Phalcon\Builder;

use Phalcon\Tools\Cli,
Phalcon\DI\Injectable;

/**
 * Manage Js templates generation
 */
class Js extends Injectable
{
    /**
     * Generate template
     * 
     * @param string $controller Controller name
     * @param null|string $actions='' Actions list separated by a comma or null for index
     */
    public function __construct(string $controller, null|string $actions=''){
        $target = $this->config->application->publicDir.'js/modules/';
        $basePath = TEMPLATE_PATH.'/js/modules/';
        $ext = 'mjs';
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

    /**
     * Create template files
     * 
     * @param string $source Source template file path
     * @param string $target Target file path
     * @param array $params Template params
     * 
     * @return void
     */
    private function create(string $source, string $target, array $params):void{
        $content = file_get_contents($source);
        $content = str_replace(['[name]', '[className]', '[path]', '[app]'], $params, $content);
        if(substr_count($params[2], '/') === 2){
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