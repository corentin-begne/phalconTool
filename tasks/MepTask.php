<?
use Phalcon\Tools\Cli;
class MepTask extends \Phalcon\CLI\Task
{
    public function mainAction($params) {
        list($env, $tag) = $params;
        if(!isset($env) || !isset($this->$config[$env])){
            die(Cli::error('Invalid env : '.$env));
        }
        if(!isset($tag)){
            die(Cli::error('Tag missing'));
        }
        if(exec('cd '.ROOT_PATH.";git branch --list $tag") !== ''){
            die(Cli::error('Tag already exists'));
        }
        $tag = $env.'_'.$tag;
        // switch to master
        exec('cd '.ROOT_PATH.';git checkout master');
        // create tag branch
        exec('cd '.ROOT_PATH.';git checkout '.$env.";git checkout -b $tag");
        $apps = glob(ROOT_PATH.'/apps', GLOB_ONLYDIR);
        foreach($apps as $app){
            // recompile less
            exec('.'.ROOT_PATH.'/phalcon generate:less --env='.ENV.' --app='.$app);
            // generate js builds
            exec('.'.ROOT_PATH.'/phalcon generate:build --env='.ENV.' --app='.$app);
        }
        // commit and push
        exec('cd '.ROOT_PATH.";git pull origin master;git add -A;git commit -am \"release\";git push origin $tag");
        /** upload files */
        exec('scp'.(isset($this->config[ENV]['mep']['key']) ? ' -i '.$this->config[ENV]['mep']['ssh'] : '').' -r '.ROOT_PATH.'/* '.$this->config[ENV]['mep']['key']);
        // switch to master
        exec('cd '.ROOT_PATH.';git checkout master');
    }
}