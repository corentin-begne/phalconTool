<?

namespace Phalcon\Builder;

class Sass extends \Phalcon\DI\Injectable
{
    public function __construct($controller, $action){
        $basePath = $this->config->application->publicDir.'css/';
        $files = array();
        if(isset($action)){
            $files = [$basePath.$controller."/$action/main.scss"];
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
                        $cdn = $this->config[ENV]->cdns[CDN];
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
            $targetFile = str_replace('.scss', '.css', $file);
            // compile and compress css
            exec('sass --style=compressed '.$file.' '.$targetFile);
            echo $targetFile."\n";
        }
        if(ENV === 'prod' && isset($content)){
            file_put_contents($config, $content);
        }
    }

    public static function globRecursive($pattern, $flags = 0){
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, self::globRecursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}