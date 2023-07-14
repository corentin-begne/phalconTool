<?

namespace Phalcon;

use Phalcon\Config\Config,
Phalcon\Autoload\Loader,
Phalcon\Di\Di;

/**
 * Manage internal library access and includes
 */
class Library extends \Phalcon\Di\Injectable{

    /**
     * Include the config type file of each librariries
     * 
     * @param string $type Name of the type [loader|route]
     * @param \Phalcon\Config\Config $config Config instance
     * @param mixed &$object instance
     * 
     * @return void
     */
    public static function get(string $type, Config $config, mixed &$object):void{        
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
     * 
     * @param array $paths Paths to include
     * 
     * @return void
     */
    public static function loadDir(array $paths):void{
        $loader = Di::getDefault()->getShared('loader');
        $loader->registerDirs($paths, true);
        $loader->register();
    }

    /**
     * Load some namespaces
     * 
     * @param array $namespaces Namespaces to include
     * 
     * @return void
     */ 
    public static function loadNamespaces(array $namespaces):void{        
        $loader = Di::getDefault()->getShared('loader');
        $loader->registerNamespaces($namespaces, true);
        $loader->register();
    }
}