<?

namespace Phalcon\Tools;

class Rest extends \Phalcon\Mvc\User\Component
{

    public static $params=[];
    public static $currentPage = 1;
    public static $limit = 20;
    public static $count = 0;
    public static $nbPage = 1;

    public static function init($restrict=true){
        self::checkReferer();
        self::checkRequest();
        self::$params = $_POST;
        self::$currentPage = isset(rest::$params['current_page']) ? (int)rest::$params['current_page'] : self::$currentPage ;
        self::$limit = isset(rest::$params['limit']) ? (int)rest::$params['limit'] : self::$limit ;
        unset(rest::$params['limit']);
        unset(rest::$params['current_page']);
        foreach(self::$params as $name => &$param){
            if($param === "true" || $param === "yes"){
                $param = true;
            } else if($param === "false" || $param === "no"){
                $param = false;
            } else if($param === ''){
                unset(self::$params[$name]);
            }
        }
    }

    public static function checkRequest(){
        if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest'){
            self::renderError("Restricted Access : Only XMLHttpRequest accepted !");
        }
    }

    public static function checkReferer(){
        $referer = $_SERVER['HTTP_REFERER'];
        $request = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $request = str_replace('/api/', '/', substr($request, 0, strrpos($request, '/')));
        if(APP === "frontend" && strpos($_SERVER['HTTP_REFERER'], 'http://'.$_SERVER["SERVER_NAME"]) === false){
            self::renderError("Restricted Access : Invalid Referer !");
        }
    }

    public static function renderError($error=''){
        self::render(false, ['error'=>$error]);
    }

    public static function renderSuccess($data=''){
        self::render(true, ['data'=>$data]);
    }

    public static function renderJson($params){
        header('Content-Type: application/json');
        die(json_encode($params));
    }

    public static function render($result, $data){
        $data['success'] = $result;
        self::renderJson($data);
    }

    public static function checkParams($list, $allowEmpty=false){
        if(!$allowEmpty && count(self::$params) === 0){
            self::renderError("No post data found !");
        }
        $missing = array();
        foreach($list as $param){
            if(!array_key_exists($param, self::$params))
            {
                $missing[] = $param;                 
            }
        }
        if(count($missing) > 0){
            self::renderError("Missing mandatory params : ".implode(', ', $missing));
        }
    }
}