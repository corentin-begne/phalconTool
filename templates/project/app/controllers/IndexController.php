<?
use Manager\User as UserManager;

class IndexController extends Phalcon\ControllerBase{

    public function indexAction(){     
        if(!UserManager::isAuthenticated()){   
            $this->response->redirect('user/login');
            return false;
        }
    }
}