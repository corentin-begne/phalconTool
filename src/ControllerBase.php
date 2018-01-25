<?
namespace Phalcon;

/**
 * Base class of all controllers
 */
class ControllerBase extends Mvc\Controller{
    /**
     * Current lang translated texts
     * @var array
     */
    public $messages;

    /**
     * Set default view data
     * @param  \Phalcon\Mvc\Dispatcher $dispatcher Applciation dispatcher
     */
    public function beforeExecuteRoute($dispatcher)
    {
        $this->view->t = $this;
        $this->view->data = [];
        $lang = $this->di->getSession()->get('lang');
        $this->view->lang = (!isset($lang)) ? explode('-', $this->request->getBestLanguage())[0] : $lang;
    }

    /**
     * Store all current lang texts translations
     * @return [type] [description]
     */
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

    /**
     * Get a translation by his label in controller/action context
     * @param  string $key    Label of the text
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function __($key, $params=[]){
        $this->getTranslation();
        return $this->messages->_($this->router->getControllerName().'_'.$this->router->getActionName().'_'.$key, $params);
    }

    public function _($key, $params=[]){
        $this->getTranslation();
        return $this->messages->_($key, $params);
    }
}