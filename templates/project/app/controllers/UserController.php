<?
use Manager\Social\GooglePlus as GooglePlusManager,
Manager\User as UserManager;

class UserController extends Phalcon\ControllerBase{

    public function loginAction(){     
        if(!UserManager::isAuthenticated()){   
            $this->response->redirect(GooglePlusManager::createAuthUrl(), true);
        } else {
            $this->response->redirect('scrud/');
        }
        return false;
    }

    public function connectAction(){
        $code = $this->request->get('code');
        if(!UserManager::isAuthenticated() && !isset($code)){
            $this->response->redirect('user/login');
        } else {
            GooglePlusManager::connect($code);
            $this->response->redirect('scrud/');
        }
        return false;
    }

    public function disconnectAction(){
        UserManager::disconnect();
        $this->response->redirect('user/login');
        return false;
    }

}