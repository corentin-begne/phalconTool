<?

namespace Phalcon\Builder;

use Phalcon\DI\Injectable;

/**
 * Manage Sass compilation
 */
class Sass extends Injectable
{
    /**
     * Compile Sass
     * 
     * @param null|string $controller=null Controller name or null for all
     * @param null|string $action Actions=null Actions list separated by a comma or null for all
     */
    public function __construct(null|string $controller=null, null|string $actions=null){
        $basePath = $this->config->application->publicDir.'css/';
        $files = [];
        if(isset($actions)){
            foreach(explode(',', $actions) as $action){
                $action .= empty($action) ? '' : '/';
                $files[] = $basePath.$controller.'/'.$action.'main.scss';
            }
        } else if(isset($controller)){
            $files = self::globRecursive($basePath.$controller."/main.scss");
        } else {
            $files = self::globRecursive($basePath."main.scss");
        }
        if(ENV === 'prod'){
            $config = $this->config->application->publicDir.'css/include/defines/config.scss';
            if(file_exists($config)){
                $content = file_get_contents($config);
                $cdn = '';
                if(isset($this->config[ENV]->cdns)){
                    if(defined('CDN')){
                        $cdn = $this->config[ENV]->cdns[constant('CDN')];
                    } else {
                        foreach($this->config[ENV]->cdns as $url){
                            $cdn = $url;
                            break;
                        }
                    }
                }
                $cdn .= '/'.APP.'/';
                file_put_contents($config, '$basePath:"'.$cdn.'";$version:'.$this->config->version.';');
            }            
        }
        foreach($files as $file){
            if(str_contains($file, '/css/include/')){
                continue;
            }
            $targetFile = str_replace('.scss', '.css', $file);
            // compile and compress css
            exec('sass --style=compressed '.$file.' '.$targetFile);
            echo $targetFile."\n";
        }
        if(ENV === 'prod' && isset($content)){
            file_put_contents($config, $content);
        }
    }

    /**
     * Browse recursivly a folder to get all main files
     * 
     * @param string $pattern Pattern of the path
     * @param null|int $flag=0 Flag for the glob function
     * 
     * @return array Paths of the main files found
     */
    public static function globRecursive(string $pattern, null|int $flags = 0):array{
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {            
            $files = array_merge($files, self::globRecursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}