<?

namespace Phalcon\Builder;

class Less extends \Phalcon\DI\Injectable
{
    public function __construct($controller, $action){
        $basePath = $this->config->application->publicDir.'css/';
        $files = array();
        if(isset($action)){
            $files = [$basePath.$controller."/$action/main.less"];
        } else if(isset($controller)){
            $files = self::globRecursive($basePath.$controller."/main.less");
        } else {
            $files = self::globRecursive($basePath."main.less");
        }
        $urlBase = defined('URL_BASE') ? 'http://'.URL_BASE : $this->config[ENV]->cdn;
        foreach($files as $file){
            $targetFile = str_replace('.less', '.css', $file);
            // compile and compress css
            exec('lessc --url-args="v='.$this->config->version.'" --global-var=\'urlBase="'.$urlBase.'"\' -sm=on -x '.$file.' '.$targetFile);
            echo $targetFile."\n";
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