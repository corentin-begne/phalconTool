<?
use Manager\Social\GooglePlus as GooglePlusManager,
Manager\User as UserManager;

class UserController extends Phalcon\ControllerBase{

    public function loginAction(){        
        $this->response->redirect(GooglePlusManager::createAuthUrl(), true);
        $this->view->disable();
    }

    public function connectAction(){
        $code = $this->request->get('code');
        if(!isset($code)){
            $this->response->redirect('user/login');
        } else {
            GooglePlusManager::connect($code);
            $this->response->redirect('scrud/');
        }
        $this->view->disable();
    }

    public function disconnectAction(){
        UserManager::disconnect();
        $this->response->redirect('user/login');
        $this->view->disable();
    }

}