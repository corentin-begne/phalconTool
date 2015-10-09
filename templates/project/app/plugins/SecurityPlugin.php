<?
use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;

/**
 * The security plugin manages the Access Control List (ACL).
 */
class SecurityPlugin extends Plugin
{
    /**
     * Get the application acl list.
     */
    private function getAcl()
    {
        $acl = new AclList();
        // deny all access by default
        $acl->setDefaultAction(Acl::DENY);

        // set roles        
        foreach(['Users', 'Guests'] as $role){
            $acl->addRole($role);
        }

        // define public/private ressources
        $privateResources = [
            'scrud' => ['*']
        ];

        $publicResources = [
           'user'  => ['login', 'connect']
        ];

        foreach ($privateResources as $resource => $actions) {
            $acl->addResource(new Resource($resource), $actions);
        }    

        foreach ($publicResources as $resource => $actions) {
            $acl->addResource(new Resource($resource), $actions);
        }

        // Grant login access only for guests
        foreach ($publicResources as $resource => $actions) {
            foreach ($actions as $action) {
                $acl->allow('Guests', $resource, $action);
            }
        }

        // Grant access to private area only for Users
        foreach ($privateResources as $resource => $actions) {
            foreach ($actions as $action) {
                $acl->allow('Users', $resource, $action);
            }
        }

        return $acl;
    }

    /**
     * Event called before each controller action.
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        // Check user data exists in session
        $user = $this->session->get('user');
        $role = isset($user) ? 'Users' : 'Guests';

        //Take the active controller/action from the dispatcher
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        //Obtain the ACL list
        $acl = $this->getAcl();
        //Check if the Role have access to the controller (resource)
        $allowed = $acl->isAllowed($role, $controller, $action);
        if ($allowed != Acl::ALLOW) {
            if($role === 'Guests'){
                $this->response->redirect('/user/login');
            } else {
                $this->response->redirect('/scrud/');
            }
            $this->view->disable();
        }
    }
}