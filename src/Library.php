<?

namespace Phalcon;

class Library extends \Phalcon\Mvc\User\Component{

    public static function get($type, $config, &$object=null){        
        if(isset($config->libraries)){
            $paths = $config->application->libDir->toArray();
            $paths[] = dirname(__FILE__).'/lib/';
            foreach($config->libraries->toArray() as $library){
                foreach($paths as $path){
                    $file = $path.$library.'/config/'.$type.'.php';
                    if(is_readable($file)){
                        include $file;
                    }
                }
            }
        }
    }

    public static function loadDir($paths){
        $loader = Di::getDefault()->getShared('loader');
        $loader->registerDirs($paths, true);
        $loader->register();
    }
 
    public static function loadNamespaces($namespaces){        
        $loader = Di::getDefault()->getShared('loader');
        $loader->registerNamespaces($namespaces, true);
        $loader->register();
    }
}