<?
namespace Manager;
use Phalcon\DI;
/**
 * Manage User connection and data
 */
class User{

    /**
     * Disconnect user from application and destroy session
     */
    public static function disconnect(){
        DI::getDefault()->getSession()->remove('user');
        DI::getDefault()->getSession()->remove('permissions');
        DI::getDefault()->getSession()->destroy();
    }

    /**
     * Connect an user to the application
     * @param  \User $user User model instance
     */
    public static function connect($user){
        DI::getDefault()->getSession()->set('user', $user->toArray());
        $ids = [];
        foreach(\UserPermission::findByUspeUserId($user['us_id']) as $permission){
            $ids[] = (int)$permission->uspe_permission_id;
        }
        DI::getDefault()->getSession()->set('permissions', $ids);
    }

    /**
     * Get user data
     * @param  string $name Data name
     * @return any       Return the wanted data or all if null
     */
    public static function get($name=null){
        if(!isset($name)){
            return DI::getDefault()->getSession()->get('user');
        } else {
            return DI::getDefault()->getSession()->get('user')[$name];
        }
    }

    /**
     * Get all user permissions
     * @return array User permission ids
     */
    public static function getPermissions(){
        return DI::getDefault()->getSession()->get('permissions');
    }

    /**
     * Check if user have a permission
     * @param  int    $id Permission id
     * @return boolean     Result of the check
     */
    public static function havePermission(int $id){
        return in_array($id, self::getPermissions());
    }

    /**
     * Check if the user is connected to the application
     * @return boolean Result of the check
     */
    public static function isAuthenticated(){
        return DI::getDefault()->getSession()->get('user') !== null;
    }

}