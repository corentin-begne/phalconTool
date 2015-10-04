<?
class UserController extends Phalcon\ControllerBase{

	public function loginAction(){
        $client = new Google_Client();
        $client->setClientId($this->config[ENV]->social->googlePlus->clientId);
        $client->setClientSecret($this->config[ENV]->social->googlePlus->clientSecret);
        $client->setDeveloperKey($this->config[ENV]->social->googlePlus->devKey);
        $client->addScope([
            Google_Service_Plus::USERINFO_EMAIL, 
            Google_Service_Plus::USERINFO_PROFILE, 
            Google_Service_Plus::PLUS_ME
        ]);
        $client->setAccessType('offline');
        $client->setApprovalPrompt("force");
        $client->setRedirectUri('http://' . $this->request->getHttpHost() .$this->config->application->baseUri.'/user/connect');
        die($client->createAuthUrl());
	}

	public function connectAction(){

	}

	public function logoutAction(){

	}

	public function disconnectAction(){

	}

}