<?

namespace Phalcon\Builder;

use Phalcon\DI\Injectable;

/**
 * Manage sass generation
 */
class Scss extends Injectable
{
    /**
     * Generate sass
     * @param string $controller Controller name
     * @param null|string $action=null Actions list separated by a comma or null for index
     */
    public function __construct(string $controller, null|string $actions=null){
        $target = $this->config->application->publicDir.'css/';
        if(isset($actions)){
            foreach(explode(',', $actions) as $action){
                $this->create($target, $controller , $action);
            }
        } else{
            $this->create($target, $controller);
        }
    }

    /**
     * Create template files
     * 
     * @param string $target Target file path
     * @param string $controller Controller name
     * @param null|string $action=null Action name, null for index
     * 
     * @return void
     */
    private function create(string $target, string $controller, null|string $action=null):void{
        $target .= $controller.'/';
        $target .= !empty($action) ? $action.'/' : '';
        exec("mkdir -p $target");
        foreach(glob(TEMPLATE_PATH.'/css/*.scss') as $source){
            $file = $target.basename($source);
            if(!file_exists($file)){
                $content = file_get_contents($source);
                if(!empty($action) && basename($source) === 'main.scss'){
                    $content = str_replace('"../', '"../../', $content);
                }
                file_put_contents($file, $content);
                echo $file."\n";
            }
        }
        new Sass($controller, $action);
    }
}