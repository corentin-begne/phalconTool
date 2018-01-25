<?

namespace Phalcon;

/**
 * Manage internal library access and includes
 */
class Library extends \Phalcon\Mvc\User\Component{

    /**
     * Include the config type file of each librariries
     * @param  string $type    Name of the type [loader|route]
     * @param  \Phalcon\Config $config  Config instance
     * @param  \Phalcon\Loader &$object Loader instance
     */
    public static function get($type, $config, &$object){        
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

    /**
     * Load some directories
     * @param  array $paths Paths to include
     */
    public static function loadDir($paths){
        $loader = Di::getDefault()->getShared('loader');
        $loader->registerDirs($paths, true);
        $loader->register();
    }

    /**
     * Load some namespaces
     * @param  array $namespaces Namespaces to include
     */ 
    public static function loadNamespaces($namespaces){        
        $loader = Di::getDefault()->getShared('loader');
        $loader->registerNamespaces($namespaces, true);
        $loader->register();
    }
}