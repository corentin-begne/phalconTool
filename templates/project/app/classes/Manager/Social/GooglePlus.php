<?
namespace Manager\Social;
use Phalcon\Library,
Phalcon\DI,
Manager\User as UserManager,
Phalcon\Mvc\Model;

class GooglePlus{    

    public static $client;

    public static function init(){
        if(isset(self::$client)){
            return false;
        }
        $di = DI::getDefault();
        Library::loadDir([$di['config']->application->rootDir.'vendor/google/apiclient/src/']);
        self::$client = new \Google_Client();
        self::$client->setClientId($di['config'][ENV]->social->googlePlus->clientId);
        self::$client->setClientSecret($di['config'][ENV]->social->googlePlus->clientSecret);
        self::$client->setDeveloperKey($di['config'][ENV]->social->googlePlus->devKey);
        self::$client->addScope([
            \Google_Service_Plus::USERINFO_EMAIL, 
            \Google_Service_Plus::USERINFO_PROFILE, 
            \Google_Service_Plus::PLUS_ME
        ]);
        /*self::$client->setAccessType('offline');
        self::$client->setApprovalPrompt("force");*/
        self::$client->setRedirectUri('http://' . $di->getRequest()->getHttpHost() .$di['config']->application->baseUri.'user/connect');
    }

    public static function createAuthUrl(){
        self::init();
        return self::$client->createAuthUrl();
    }

    public static function connect($code){
        self::init();
        self::$client->authenticate($code);
        $plus = new \Google_Service_Plus(self::$client);
        $userPlus = $plus->people->get('me');
        // get user
        Model::setup([
            'columnRenaming' => false
        ]);
        try{
        $user = \User::findFirstByEmail($userPlus->emails[0]->value);
            if(!$user){ // create if not exists
                $user = new \User();
                $user->create([
                    'email' => $userPlus->emails[0]->value,
                    'name' => $userPlus->displayName,
                    'gender_id' => \GenderType::findFirstByName(!isset($userPlus->gender) ? 'female' : $userPlus->gender)->id,
                    'social_id' => \SocialType::findFirstByName('googlePlus')->id,
                    'lang_id' => \LangType::findFirstByName(!isset($userPlus->language) ? 'en' : $userPlus->language)->id
                ]);
                $userSocial = new \UserSocial();
                $userSocial->create([
                    'id' => $userPlus->id,
                    'user_id' => $user->id,
                    'is_verified' => $userPlus->verified,
                    'token' => self::$client->getAccessToken()
                ]);
            }
        } catch(\Phalcon\Mvc\Model\Exception $e){
            die($e->getMessage());
        }
        UserManager::connect($user);
    }

}