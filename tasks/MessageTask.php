<?
class MessageTask extends \Phalcon\CLI\Task
{
    public function mainAction() {

    }

    public function importAction() {
        $files = glob($this->config->application->rootDir.'/apps/'.APP.'/messages/*.php');
        $this->di->get('db')->query('delete from LangMessage');
        foreach($files as $file){
            include($file);
            $langName = basename($file,'.php');
            $lang = LangType::findFirstByLaty_name($langName);
            foreach($messages as $name => $message){
                $langMessage = new LangMessage([
                    'lame_name'=>$name,
                    'lame_value'=>$message,
                    'lame_lang_id'=>$lang->laty_id
                ]);
                $langMessage->save();
            }
        }
    }

    public function exportAction() {
        $template = '<?
$messages = [
';
        $part = "   '[name]' => '[value]',\n";
        $tmp = time();
        $path = $this->config->application->rootDir.'/apps/'.APP.'/messages/';
        if(!defined('SAVE')){
            exec('rm -rf '.$path.'*.php');
        } else {
            exec('mkdir '.$path.$tmp);
            exec('mv '.$path.'*.php '.$path.$tmp);
        }
        $result = [];
        $messages = LangMessage::find();
        foreach($messages as $message){
            $lang = $message->lang_type_lang_id->laty_name;
            if(!isset($result[$lang])){
                $result[$lang] = $template;
            }
            $result[$lang] .= str_replace(['[name]', '[value]'],[$message->lame_name, htmlentities($message->lame_value, ENT_QUOTES)],$part);
        }
        foreach($result as $lang => $content){
            $content = $content.'];';
            file_put_contents($path.$lang.'.php', $content);
        }
    }
}