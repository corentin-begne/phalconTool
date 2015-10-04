<?
namespace Manager;
use Phalcon\DI;
class User{

    public static function disconnect(){
        DI::getDefault()->getSession()->remove('user');
        DI::getDefault()->getSession()->destroy();
    }

    public static function connect($user){
        DI::getDefault()->getSession()->set('user', $user->toArray());
    }

    public static function get($name){
        return DI::getDefault()->getSession()->get('user')[$name];
    }

}