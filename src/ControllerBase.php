<?
namespace Phalcon;

use Phalcon\Mvc\Dispatcher;

/**
 * Base class of all controllers
 */
class ControllerBase extends Mvc\Controller{
    /**
     * Current lang translated texts
     * @var array
     */
    public array $messages = [];

    /**
     * Set default view data
     * 
     * @param \Phalcon\Mvc\Dispatcher $dispatcher Applciation dispatcher
     * 
     * @return void
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher):void
    {
        if($this->request->has('PHPSESSID')){
            $sessionId = $this->request->get('PHPSESSID');
            
            if($sessionId !== $this->session->getId()){
                $this->session->destroy();
                $this->session->setId($sessionId)->start();
            }
        }
        $this->view->t = $this;
        $this->view->data = [];
        $this->view->lang = $this->lang;
    }

    /**
     * Get a translation by his label in controller/action context
     * 
     * @param string $key Label of the text
     * @param null|array $params=[] Data to bind
     * 
     * @return string Text translated in the correct lang
     * 
     * @example
     * <!-- Label: controller_action_hi-name => 'Hello %name%' -->
     * <p><?=$t->__("hi-name", ["name" => $name])?></p>
     */
    public function __(string $key, null|array $params=[]):string{
        return $this->translation->_($this->router->getControllerName().'_'.$this->router->getActionName().'_'.$key, $params);
    }

    /**
     * Get a translation by his label in global context
     * 
     * @param string $key Label of the text
     * @param null|array $params=[] Data to bind
     * 
     * @return string Text translated in the correct lang
     * 
     * @example
     * <!-- Label: hi-name => 'Hello %name%' -->
     * <p><?=$t->__("hi-name", ["name" => $name])?></p>
     */
    public function _(string $key, null|array $params=[]):string{
        return $this->translation->_($key, $params);
    }
}