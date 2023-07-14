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
        $file = str_replace("'dev'", "'ENV'", file_get_contents($this->config->application->rootDir.'public/init.php'));
        file_put_contents($this->config->application->rootDir.'public/init.php', $file);
        if(ENV !== 'dev'){
            exec($this->config->application->rootDir.'phalcon generate:build --module --env='.ENV);
            exec($this->config->application->rootDir.'phalcon generate:sass --env='.ENV);
        }
        /** upload files */
        $exclude = '" --exclude="';
        $cmd = 'rsync -zvr --links '.(isset($this->config[ENV]['mep']['args']) ? ' '.$this->config[ENV]['mep']['args'].' ' : '').(isset($this->config[ENV]['mep']['key']) ? '-e "ssh -i '.$this->config[ENV]['mep']['key'].'" ' : '').(isset($this->config[ENV]['mep']['excludes']) ? '--exclude="'.implode($exclude, $this->config[ENV]['mep']['excludes']->toArray()).'" ' : '').$this->config->application->rootDir.'* '.$this->config[ENV]['mep']['ssh'];
        exec($cmd);
        if(ENV !== 'dev'){
            exec($this->config->application->rootDir.'phalcon generate:sass --env=dev');                  
        }
        $file = str_replace("'ENV'", "'dev'", $file);  
        file_put_contents($this->config->application->rootDir.'public/init.php', $file);
    }
}