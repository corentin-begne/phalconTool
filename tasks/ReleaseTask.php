<?
use \Phalcon\CLI\Task;

/**
 * Task managing release
 */
class ReleaseTask extends Task
{
    /**
     * Send release to server using rsync, build sass and js before
     * 
     * @return void
    */    
    public function mainAction():void{
        // need to change the env in the init file
        $file = str_replace("'dev'", "'".ENV."'", file_get_contents($this->config->application->rootDir.'public/init.php'));
        file_put_contents($this->config->application->rootDir.'public/init.php', $file);
        if(defined('BUILD')){
            exec($this->config->application->rootDir.'phalcon generate:build');
            exec($this->config->application->rootDir.'phalcon generate:sass --env='.ENV);
        }
        /** upload files */
        $exclude = '" --exclude="';
        $cmd = 'rsync -zvr --links '.(isset($this->config[ENV]['release']['args']) ? ' '.$this->config[ENV]['release']['args'].' ' : '').(isset($this->config[ENV]['release']['key']) ? '-e "ssh -i '.$this->config[ENV]['release']['key'].'" ' : '').(isset($this->config[ENV]['release']['excludes']) ? '--exclude="'.implode($exclude, $this->config[ENV]['release']['excludes']->toArray()).'" ' : '').realpath($this->config->application->rootDir).'/ '.$this->config[ENV]['release']['ssh'];
        echo $cmd."\n";
        exec($cmd);
        if(isset($this->config[ENV]['release']['post_hook'])){
            $cmd = 'rsync -zvr '.(isset($this->config[ENV]['release']['post_hook']['args']) ? ' '.$this->config[ENV]['release']['post_hook']['args'].' ' : '').(isset($this->config[ENV]['release']['key']) ? '-e "ssh -i '.$this->config[ENV]['release']['key'].'" ' : '').(isset($this->config[ENV]['release']['post_hook']['excludes']) ? '--exclude="'.implode($exclude, $this->config[ENV]['release']['post_hook']['excludes']->toArray()).'" ' : '').$this->config[ENV]['release']['post_hook']['path'].' '.$this->config[ENV]['release']['ssh'].'/'.$this->config[ENV]['release']['post_hook']['path'];
            echo 'post hook command : '.$cmd."\n";
            exec($cmd);
        }
        if(defined('BUILD')){
            exec($this->config->application->rootDir.'phalcon generate:sass --env=dev');                  
        }
        $file = str_replace("'".ENV."'", "'dev'", $file);  
        file_put_contents($this->config->application->rootDir.'public/init.php', $file);
    }
}