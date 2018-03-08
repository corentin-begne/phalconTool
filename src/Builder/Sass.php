<?

namespace Phalcon\Builder;

class Sass extends \Phalcon\Mvc\User\Component
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
        foreach($files as $file){
            $targetFile = str_replace('.scss', '.css', $file);
            // compile and compress css
            exec('sass --style=compressed '.$file.' '.$targetFile);
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