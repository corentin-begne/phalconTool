<?
use Phalcon\Tools\Cli,
\Phalcon\CLI\Task;
/**
 * Task Managing translations import / export
 */
class MessageTask extends Task
{
    /**
     * Main task action (not implemented)
     */
    public function mainAction():void {

    }

    /**
     * Import translations files into database
     * 
     * @return void
     */
    public function importAction():void {
        $files = glob($this->config->application->messagesDir.'*.php');
        $this->di->get('db')->query('delete from LangMessage');
        foreach($files as $file){
            include($file);
            $langName = basename($file,'.php');
            $lang = LangType::findFirstByLaty_name($langName);
            foreach($messages as $name => $message){
                $langMessage = new LangMessage([
                    'lame_name'=>$name,
                    'lame_value'=>html_entity_decode($message),
                    'lame_lang_id'=>$lang->laty_id
                ]);
                $langMessage->save();
            }
        }
    }

    /**
     * Export translations from database to files
     * 
     * @return void
     */
    public function exportAction():void {
        $template = '<?
$messages = [
';
        $part = "   '[name]' => '[value]',\n";
        $tmp = time();
        $path = $this->config->application->messagesDir;
        if($path === ''){
            Cli::error('messagesDir path is empty');
        }
        $result = [];
        $messages = LangMessage::find();
        foreach($messages as $message){
            $lang = $message->lang_type_lang_id->laty_name;
            if(!isset($result[$lang])){
                $result[$lang] = $template;
            }
            $result[$lang] .= str_replace(['[name]', '[value]'],[$message->lame_name, str_replace('\'', "\'", $message->lame_value)],$part);
        }
        foreach($result as $lang => $content){
            $content = $content.'];';
            file_put_contents($path.$lang.'.php', $content);
        }
    }
}