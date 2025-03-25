<?
use Phalcon\Acl\Enum,
Phalcon\Acl\Role,
Phalcon\Acl\Adapter\Memory as AclList,
Phalcon\DI\Injectable,
Phalcon\Dispatcher\Exception as DispatcherException,
Manager\User as UserManager,
Phalcon\Events\Event,
Phalcon\Mvc\Dispatcher;

/**
 * The security plugin manages the Access Control List (ACL).
 */
class SecurityPlugin extends Injectable
{
    
    /**
     * Get all actions of a controller
     * 
     * @param string $class Name of the class, should be a controller
     * 
     * @return array Actions found
     */
    private function getActions(string $class):array{
        $result = [];
        foreach(get_class_methods($class) as $fn){
            if(strpos($fn, 'Action') !== false){
                $result[] = str_replace('Action', '', $fn);
            }
        }
        return $result;
    }

    /**
     * Get the application acl list.
     * 
     * @return \Phalcon\Acl\Adapter\Memory Acl list instance
     */
    private function getAcl():AclList
    {
        $acl = new AclList();
        // deny all access by default
        $acl->setDefaultAction(Enum::DENY);
        $permissions = [];
        // set roles        
        foreach(PermissionType::find() as $permission){
            $acl->addRole(new Role($permission->pety_id));
            $permissions[$permission->pety_name] = $permission->pety_id;
        }

        foreach([
            $this->config->application->controllersDir.'*Controller.php',
       //     $this->config->application->rootDir.'vendor/v-cult/phalcon/src/lib/*/*Controller.php'
        ] as $path){
            foreach(glob($path) as $controller){
                $controller = basename($controller, '.php');
                $acl->addComponent(lcfirst(str_replace('Controller', '', $controller)), $this->getActions($controller));
            }
        }

        // define public/private ressources
        $private = [
            'index' => '*',
            'user'=> ['disconnect']
        ];

        $public = [
           'user'  => ['login', 'connect']
        ];


        foreach(['private', 'public'] as $type){
            foreach($$type as $resource => $actions){
                switch($type){
                    case 'private':
                        $acl->allow($permissions['admin'], $resource, $actions);
                        $acl->allow($permissions['user'], $resource, $actions);
                        break;
                    case 'public':
                        $acl->allow($permissions['anonymous'], $resource, $actions);
                        break;
                }
            }
        }

        return $acl;
    }

    /**
     * Redirect user to default route if no controller/action found
     * 
     * @param \Phalcon\Events\Event $event Event of the request
     * @param \Phalcon\Mvc\Dispatcher $dispatcher Application dispatcher
     * @param Exception $exception Current Exception
     * 
     * @return void
     */
    public function beforeException(Event $event, Dispatcher $dispatcher, Exception $exception):void {
        switch ($exception->getCode()) {
            case DispatcherException::EXCEPTION_HANDLER_NOT_FOUND:
            case DispatcherException::EXCEPTION_ACTION_NOT_FOUND:
                $this->redirectUser();
        }
    }

    /**
     * Redirect user to default route
     * 
     * @return void
     */
    private function redirectUser():void{
        if(!UserManager::isAuthenticated()){
            $this->response->redirect('user/login');
        } else {
            $this->response->redirect('');
        }
        $this->response->send();
        die;
    }

    /**
     * Check user permissions vs ACL and redirect to default route if not allowed
     * 
     * @return void
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher):void
    {

        //Take the active controller/action from the dispatcher
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        //Obtain the ACL list
        $acl = $this->getAcl();
        //Check if the Role have access to the controller (resource)
        $allowed = $acl->isAllowed(UserManager::getPermission(), $controller, $action);
        if($allowed != Enum::ALLOW) {
            $this->redirectUser();
            $this->view->disable();
        }
    }
}