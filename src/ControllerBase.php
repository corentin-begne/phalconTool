<?
namespace Phalcon;

class ControllerBase extends Mvc\Controller{
    public $messages;

    public function beforeExecuteRoute($dispatcher)
    {
        $this->view->t = $this;
        $this->view->data = [];
        $lang = $this->di->session->get('lang');
        $this->view->lang = (!isset($lang)) ? explode('-', $this->request->getBestLanguage())[0] : $lang;
    }

    public function getTranslation()
    {
        if(isset($this->messages)){
            return false;
        }

        $language = $this->view->lang;

        if (file_exists($this->config->application->messagesDir.$language.'.php')) {
            require $this->config->application->messagesDir.$language.'.php';
        } else {
            require $this->config->application->messagesDir.'en.php';
        }

        // Return a translation object
        $this->messages = new Translate\Adapter\NativeArray(['content'=>$messages]);
    }

    public function __($key, $params=[]){
        $this->getTranslation();
        return $this->messages->_($this->router->getControllerName().'_'.$this->router->getActionName().'_'.$key, $params);
    }

    public function _($key, $params=[]){
        $this->getTranslation();
        return $this->messages->_($key, $params);
    }
}