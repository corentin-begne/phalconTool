<?
use Phalcon\Tools\Cli;
class MepTask extends \Phalcon\CLI\Task
{
    public function mainAction($params) {
        list($env, $tag) = $params;
        if(!isset($env) || !isset($this->config[$env])){
            die(Cli::error('Invalid env : '.$env));
        }
        if(!isset($tag)){
            die(Cli::error('Tag missing'));
        }
        $tag = $env.'_'.$tag;
        if(exec('cd '.$this->config->application->rootDir.";git branch --list $tag") !== ''){
            die(Cli::error('Tag already exists'));
        }  
        // switch to master
        exec('cd '.$this->config->application->rootDir.';git checkout master');
        // create tag branch
        exec('cd '.$this->config->application->rootDir.';git checkout '.$env.";git checkout -b $tag");
        $apps = glob($this->config->application->rootDir.'apps/*', GLOB_ONLYDIR);
        if(!defined('NO_BUILD')){
            foreach($apps as $app){
                // recompile less
                exec('cd '.$this->config->application->rootDir.';./phalcon generate:less --env='.$env.' --app='.basename($app));
                // generate js builds
                exec('cd '.$this->config->application->rootDir.';./phalcon generate:build --env='.$env.' --app='.basename($app));
            }
        }
        // need to change the env in the init file
        $file = str_replace("'dev'", "'$env'", file_get_contents($this->config->application->rootDir.'public/init.php'));
        file_put_contents($this->config->application->rootDir.'public/init.php', $file);
        // commit and push
        exec('cd '.$this->config->application->rootDir.";git add -A;git commit -am \"release\";git push origin $tag");
        /** upload files */
        $exclude = '" --exclude="';
        $cmd = 'rsync -zvr '.(isset($this->config[$env]['mep']['key']) ? '-e "ssh -i '.$this->config[$env]['mep']['key'].'" ' : '').(isset($this->config[$env]['mep']['excludes']) ? '--exclude="'.implode($exclude, $this->config[$env]['mep']['excludes']->toArray()).'" ' : '').$this->config->application->rootDir.'* '.$this->config[$env]['mep']['ssh'];
        exec($cmd);
        // switch to master
        exec('cd '.$this->config->application->rootDir.';git checkout master');
    }
}