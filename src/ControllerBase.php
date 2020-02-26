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
        $this->view->lang = $this->lang;
    }

    /**
     * Get a translation by his label in controller/action context
     * @param  string $key    Label of the text
     * @param  array  $params Data to bind
     * @return string         Text translated in the correct lang
     * @example
     * <!-- Label: controller_action_hi-name => 'Hello %name%' -->
     * <p><?=$t->__("hi-name", ["name" => $name])?></p>
     */
    public function __($key, $params=[]){
        return $this->translation->_($this->router->getControllerName().'_'.$this->router->getActionName().'_'.$key, $params);
    }

    /**
     * Get a translation by his label in global context
     * @param  string $key    Label of the text
     * @param  array  $params Data to bind
     * @return string         Text translated in the correct lang
     * @example
     * <!-- Label: hi-name => 'Hello %name%' -->
     * <p><?=$t->__("hi-name", ["name" => $name])?></p>
     */
    public function _($key, $params=[]){
        return $this->translation->_($key, $params);
    }

    public function _d($key, $params=[]){
        return html_entity_decode($this->translation->_($key, $params));
    }
}