<?
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Assets\Filters\Jsmin;
/**
 * The Assets plugin manages all assets (CSS/JS) on application.
 */
class AssetsPlugin extends Component
{
    /**
     * The event is called before the controller action.
     *
     * We create collection for assets.
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $currentPath = ($dispatcher->getActionName() === 'index') ? $dispatcher->getControllerName() : $dispatcher->getControllerName().'/'.$dispatcher->getActionName(); 

        $this->assets->collection('js')
        ->setPrefix(APP.'/js/')
        ->addJs("$currentPath/manager.js")
        ->addJs("$currentPath/main.js");
        
        $this->assets->collection('css')
        ->setPrefix(APP.'/css/')
        ->addCss("$currentPath/main.css");
        
    }
}