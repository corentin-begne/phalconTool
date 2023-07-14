<?
use Phalcon\Events\Event,
Phalcon\DI\Injectable,
Phalcon\Mvc\Dispatcher;

/**
 * The Assets plugin manages all assets (CSS/JS) on application.
 */
class AssetsPlugin extends Injectable
{
    /**
     * The event is called before the controller action.
     * Create assets collections.
     * 
     * @param \Phalcon\Events\Event $event Application event
     * @param \Phalcon\Mvc\Dispatcher $dispatcher Application dispatcher
     * 
     * @return void
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher):void
    {
        $currentPath = ($dispatcher->getActionName() === 'index') ? $dispatcher->getControllerName() : $dispatcher->getControllerName().'/'.$dispatcher->getActionName(); 
        
        $this->assets->collection('libjs')
        ->setPrefix('/lib/'.$dispatcher->getControllerName().'/public/js/');

        $this->assets->collection('mjs')
        ->setPrefix(APP.'/js/modules/');

        $prefix = in_array($dispatcher->getControllerName(), $this->config->libraries->toArray()) ? 'lib' : '';

        $this->assets->collection($prefix.'mjs')
        ->addJs("$currentPath/".((ENV!=="dev") ? 'build' : 'main').'.mjs');
        
        $this->assets->collection('libcss')
        ->setPrefix('/lib/'.$dispatcher->getControllerName().'/public/css/');

        $this->assets->collection('css')
        ->setPrefix(APP.'/css/');
        
        $this->assets->collection($prefix.'css')
        ->addCss("$currentPath/main.css");
        
    }

    /**
     * Trigre after dispatch to set full path to assets
     * 
     * @param \Phalcon\Events\Event $event Application event
     * @param \Phalcon\Mvc\Dispatcher $dispatcher Application dispatcher
     * 
     * @return void
     */
    public function afterDispatch(Event $event, Dispatcher $dispatcher):void
    {
        foreach($this->assets->getCollections() as $name => $collection){
            foreach($this->assets->collection($name) as $ressource){
                $ressource->setPath($ressource->getPath().'?v='.$this->config->version); 
            }
        }
    }
}