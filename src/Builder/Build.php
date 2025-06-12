<?
namespace Phalcon\Builder;
use Phalcon\Tools\Cli,
Phalcon\DI\Injectable;

/**
 * Manage Js builds generation
 */
class Build extends Injectable
{
    /**
     * Path to rollup bin
     * @var string
     */
    private string $rollup;
    /**
     * Path to plugin alias bin used for importmap
     * @var string
     */
    private string $importMap;
    /**
     * Path to uglifyjs bin
     * @var string
     */
    private string $uglify;
    /**
     * Path to frontend modules dir
     * @var string
     */
    private string $basePath;
    /**
     * Extension used for js file
     * @var string
     */
    private string $ext;
    /**
     * Build format
     * @var string
     */
    private string $format;

    /**
     * Generate builds for all controllers and actions without any params
     * 
     * @param null|string $controller=null Controller Name
     * @param null|string $action=null Actions list separated by a comma or null for all
     */
    public function __construct(null|string $controller=null, null|string $actions=null){     
        $this->rollup = $this->config->application->rootDir.'/node_modules/rollup/dist/bin/rollup';
        $this->importMap = $this->config->application->rootDir.'/node_modules/@rollup/plugin-alias/dist/cjs/index.js';
        $this->uglify = $this->config->application->rootDir.'/node_modules/uglify-js/bin/uglifyjs';
        $this->basePath = $this->config->application->publicDir.'js/modules/';
        $this->ext = 'mjs';
        $this->format = defined('FORMAT') ? FORMAT : 'es';                      
        if(isset($controller)){
            if(isset($actions)){
                foreach(explode(',', $actions) as $action){
                    $action .= empty($action) ? '' : '/';
                    $this->run($this->basePath.$controller.'/'.$action.'main.'.$this->ext);
                }
            } else {
                foreach($this->globRecursive($this->basePath.$controller.'/main.'.$this->ext) as $mainPath){
                    $this->run($mainPath);
                }
            }
        } else {
            foreach($this->globRecursive($this->config->application->publicDir.'main.'.$this->ext) as $mainPath){
                $this->run($mainPath);
            }
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
    private function globRecursive(string $pattern, null|int $flag=0):array{
        $files = glob($pattern, $flag);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, $this->globRecursive($dir.'/'.basename($pattern), $flag));
        }
        return $files;
    }

    /**
     * Excute bash command and get the result
     * 
     * @param string $cmd Command to execute
     * 
     * @return string Result of the command
     */
    private function exec(string $cmd):string{
        $result = exec($cmd.' >&1 2>&1', $r);
        if($result === false){
            return $result;
        } else {
            return implode("\n", $r);
        }
    }

    /**
     * Build the main file
     * 
     * @param string $main Path of the main file
     * 
     * @return void
     */
    private function run(string $main):void{   
        if(!file_exists($main)){
            return;
        }   
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
                $entries .= '{find:'.$exp.', replacement:"'.$this->config->application->rootDir.'public/'.(defined('APP_NAME')?APP_NAME:'').$path.'"},';
            }
            $cmd .= ' --plugin \''.$this->importMap.'={entries:['.trim($entries, ',').']}'.'\'';
        }
        $result = $this->exec($cmd);
        if($result === false){
            Cli::error('rollup command failed');
        }
        $result .= "\n".$this->exec($this->uglify.' '.$build.' -c if_return=false,conditionals=false -m -o '.$build);
        if(defined('DEBUG')){
            echo "\n".trim($result)."\n\n";
        }
        if(file_exists($build)){
            echo $build."\n";
        } else {
            Cli::warning("build failed", true);
            echo "\n".trim($result)."\n\n";
        }
    }
}