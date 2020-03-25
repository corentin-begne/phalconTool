<?
use Phalcon\Tools\Cli;
class ReleaseTask extends \Phalcon\CLI\Task
{
    public function mainAction($env, $tag) {
        @list($env, $tag) = $params;
        if(!isset($this->config[$env])){
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
                if(defined('COMPILE_LESS')){
                    exec('cd '.$this->config->application->rootDir.';./phalcon generate:less --env='.$env.' --app='.basename($app));
                } else {
                    exec('cd '.$this->config->application->rootDir.';./phalcon generate:sass --env='.$env.' --app='.basename($app));
                }
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
        $cmd = 'rsync -zvr --links '.(isset($this->config[$env]['mep']['key']) ? '-e "ssh -i '.$this->config[$env]['mep']['key'].'" ' : '').(isset($this->config[$env]['mep']['excludes']) ? '--exclude="'.implode($exclude, $this->config[$env]['mep']['excludes']->toArray()).'" ' : '').$this->config->application->rootDir.'* '.$this->config[$env]['mep']['ssh'];
        exec($cmd);
        // switch to master
        exec('cd '.$this->config->application->rootDir.';git checkout master');
    }
}