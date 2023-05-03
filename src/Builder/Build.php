<?

namespace Phalcon\Builder;
use Phalcon\Tools\Cli;
class Build extends \Phalcon\DI\Injectable
{
    public function __construct($controller, $action){     
        $this->rollup = $this->config->application->rootDir.'/node_modules/rollup/dist/bin/rollup';
        $this->importMap = $this->config->application->rootDir.'/node_modules/@rollup/plugin-alias/dist/cjs/index.js';
        $this->uglify = $this->config->application->rootDir.'/node_modules/uglify-js/bin/uglifyjs';
        $this->basePath = $this->config->application->publicDir.'js/'.(defined('MODULE')?'modules/':'');
        $this->ext = (defined('MODULE')?'m':'').'js';
        $this->format = defined('MODULE')?'es':'iife';        
        \Phalcon\Library::loadDir([$this->config->application->controllersDir]);
        $actions = explode(',', $action);        
        if(isset($controller)){
            if(isset($action)){
                foreach($actions as $action){
                    $this->run($controller, $action);
                }
            } else {
                foreach($this->getActions(ucfirst($controller).'Controller') as $action){
                    $this->run($controller, $action);
                }
            }
        } else {
            foreach($this->globRecursive($this->config->application->publicDir.'main.'.$this->ext) as $mainPath){
                $this->run($mainPath);
            }
        } 
    }

    public function globRecursive($pattern, $flags = 0){
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, $this->globRecursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }

    private function exec($cmd){
        $result = exec($cmd.' >&1 2>&1', $r);
        if($result === false){
            return $result;
        } else {
            return implode("\n", $r);
        }
    }

    public function run($main){      
        $build = substr($main, 0, strrpos($main, '/')+1).'build.'.$this->ext;
        if(file_exists($build)){
            exec('rm -f '.$build);
        }        
        $cmd = $this->rollup.' '.$main.' --file '.$build.' --format '.$this->format;
        if(file_exists($this->config->application->rootDir.'public/importmap.json')){
            $maps = json_decode(file_get_contents($this->config->application->rootDir.'public/importmap.json'), true);
            $entries = '';
            foreach($maps['imports'] as $name => $path){
                $exp = str_contains($name, '/') ? '/^'.str_replace('/', '\/', $name).'/' : '/^'.$name.'$/';
                $entries .= '{find:'.$exp.', replacement:"'.$this->config->application->rootDir.'public/'.APP_NAME.$path.'"},';
            }
            $cmd .= ' --plugin \''.$this->importMap.'={entries:['.trim($entries, ',').']}'.'\'';
        }
        $result = $this->exec($cmd);
        if($result === false){
            Cli::error('rollup command failed');
        }
        $result .= "\n".$this->exec($this->uglify.' '.$build.' -c -o '.$build);
        if(defined('DEBUG')){
            echo "\n".trim($result)."\n\n";
        }
        if(file_exists($build)){
            echo $build."\n";
        } else {
            Cli::warning("build failed", true);
            echo "\n".trim($result)."\n\n";
        }
        //exec('cd '.$this->config->application->publicDir.'../;make '.ENV.' APP='.APP.' ACTION='.$action.' CONTROLLER='.$controller);
    }
}