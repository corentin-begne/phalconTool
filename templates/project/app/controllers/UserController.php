<?
use Manager\User as UserManager,
Phalcon\ControllerBase;

class UserController extends ControllerBase{

    /**
     * Login user action, initialize the form
     * 
     * @return void
     */
    public function loginAction():void{     
        $this->view->form = new UserForm(new User(), [
            'autofocus'=>'us_email'
        ]);
    }

    /**
     * Rest service to connect an user
     * 
     * @return void
     */
    public function connectAction():void{
        Rest::init();
        Rest::checkParams(['us_email', 'us_password']);
        $user = User::findFirstByUsEmail(Rest::$params['us_email']);
        if($user === null){
            Rest::renderError();
        }
        if($user->us_password !== md5(Rest::$params['us_password'])){
            Rest::renderError();
        }
        UserManager::connect($user->toArray());
        Rest::renderSuccess();
    }

    /**
     * Disconnect an user and redirect to login page
     * 
     * @return void
     */
    public function disconnectAction():void{
        UserManager::disconnect();
        $this->response->redirect('user/login');
    }

}