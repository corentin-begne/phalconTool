<?
use Phalcon\Acl\Enum;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\DI\Injectable;
use Phalcon\Dispatcher\Exception;
use Manager\User as UserManager;

/**
 * The security plugin manages the Access Control List (ACL).
 */
class SecurityPlugin extends Injectable
{
    private function getActions($class){
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
     */
    private function getAcl()
    {
        $acl = new AclList();
        // deny all access by default
        $acl->setDefaultAction(Enum::DENY);
        $permissions = [];
        // set roles        
        foreach(PermissionType::find() as $permission){
            $acl->addRole($permission->pety_id);
            $permissions[$permission->pety_name] = $permission->pety_id;
        }

        foreach([
            $this->config->application->controllersDir.'*Controller.php',
            $this->config->application->rootDir.'vendor/v-cult/phalcon/src/lib/*/*Controller.php'
        ] as $path){
            foreach(glob($path) as $controller){
                $controller = basename($controller, '.php');
                $acl->addComponent(lcfirst(str_replace('Controller', '', $controller)), $this->getActions($controller));
            }
        }

        // define public/private ressources
        $private = [
            'index' => '*',
            'scrud'=> '*',
            'api'=> '*'
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
     * @param  \Phalcon\Events\Event $event      Event of the request
     * @param  \Phalcon\Mvc\Dispatcher $dispatcher Application dispatcher
     * @param  \Phalcon\Exception         $exception  Current Exception
     * @return boolean             Return false on exception found to stop propagation to view engine
     */
    public function beforeException($event, $dispatcher, $exception) {
        switch ($exception->getCode()) {
            case Exception::EXCEPTION_HANDLER_NOT_FOUND:
            case Exception::EXCEPTION_ACTION_NOT_FOUND:
                $this->redirectUser();
                return false;
        }
    }

    /**
     * Redirect user to default route
     */
    private function redirectUser(){
        if(!UserManager::isAuthenticated()){
            $this->response->redirect('/user/login');
        } else {
            $this->response->redirect('/');
        }
    }

    /**
     * Check user permissions vs ACL and redirect to default route if not allowed
     */
    public function beforeDispatch($event, $dispatcher)
    {
        // Check user data exists in session
        $permissions = UserManager::getPermissions();

        //Take the active controller/action from the dispatcher
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        //Obtain the ACL list
        $acl = $this->getAcl();
        //Check if the Role have access to the controller (resource)
        foreach($permissions as $permission){
            $allowed = $acl->isAllowed($permission, $controller, $action);
            if ($allowed === Enum::ALLOW) {
                break;
            }
        }
        if($allowed != Enum::ALLOW) {
            $this->redirectUser();
            $this->view->disable();
        }
    }
}