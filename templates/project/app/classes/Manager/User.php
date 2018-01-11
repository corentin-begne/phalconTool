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
        $ids = [];
        foreach(\UserPermission::findByUspeUserId($user['us_id']) as $permission){
            $ids[] = (int)$permission->uspe_permission_id;
        }
        DI::getDefault()->getSession()->set('permissions', $ids);
    }

    public static function get($name=null){
        if(!isset($name)){
            return DI::getDefault()->getSession()->get('user');
        } else {
            return DI::getDefault()->getSession()->get('user')[$name];
        }
    }

    public static function getPermissions(){
        return DI::getDefault()->getSession()->get('permissions');
    }

    public static function havePermission(int $id){
        return in_array($id, DI::getDefault()->getSession()->get('permissions'));
    }

    public static function isAuthenticated(){
        return DI::getDefault()->getSession()->get('user') !== null;
    }

}