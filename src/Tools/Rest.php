<?
/**
 * Manage Rest API request / response<br>
 * <u><b>Example :</b></u>
 * ```php
 * Rest::init();
 * Rest::checkParams(['id']);
 * try{
 *     Rest::renderSuccess();
 * } catch(Exception $e){
 *     Rest::renderError($e->getMessage());
 * }
 * ```
 */
class Rest
{
    /**
     * Request post data
     * @var array
     */
    public static $params=[];
    /**
     * Current page number
     * @var integer
     */
    public static $currentPage = 1;
    /**
     * Limit of result by page
     * @var integer
     */
    public static $limit = 20;
    /**
     * Current count relative to the page and limit ($limit*$currentpage)
     * @var integer
     */
    public static $count = 0;
    /**
     * The total of the request page
     * @var integer
     */
    public static $nbPage = 1;

    /**
     * Check the conformity of the request and get the post data
     * @param  boolean $restrict Allow to disable the server name check restriction
     */
    public static function init($restrict=true){
        if($restrict){
            self::checkReferer();
            self::checkRequest();
        }
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

    /**
     * Check if the request is a XMLHttpRequest
     */
    public static function checkRequest(){
        if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest'){
            self::renderError("Restricted Access : Only XMLHttpRequest accepted !");
        }
    }

    /**
     * Check if the referer corresponding to the same server 
    */
    public static function checkReferer(){
        $referer = $_SERVER['HTTP_REFERER'];
        $request = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $request = str_replace('/api/', '/', substr($request, 0, strrpos($request, '/')));
        if(APP === "frontend" && strpos($_SERVER['HTTP_REFERER'], 'http://'.$_SERVER["SERVER_NAME"]) === false && strpos($_SERVER['HTTP_REFERER'], 'https://'.$_SERVER["SERVER_NAME"]) === false){
            self::renderError("Restricted Access : Invalid Referer !");
        }
    }

    /**
     * Rest response on error
     * @param  any $error Data corresponding to the error, can be any type
     */
    public static function renderError($error=''){
        self::render(false, ['error'=>$error]);
    }

    /**
     * Rest response on success
     * @param  any $data Data render on success, can be any type
     */
    public static function renderSuccess($data=''){
        self::render(true, ['data'=>$data]);
    }

    /**
     * Rest response
     * @param  array $params Data to render
     */
    public static function renderJson($params=[]){
        header('Content-Type: application/json');
        die(json_encode($params));
    }

    /**
     * Normalize data to render
     * @param  boolean $result Specify is the response is on success or error
     * @param  array $data   Data to render
     */
    public static function render($result, $data=[]){
        $data['success'] = $result;
        self::renderJson($data);
    }

    /**
     * Check the presence of required params
     * @param  type  $list  List of params name to check
     * @param  boolean $allowEmpty Set to true if post data can be empty
     */
    public static function checkParams($list=[], $allowEmpty=false){
        if(!$allowEmpty && count(self::$params) === 0){
            self::renderError("No post data found !");
        }
        $missing = [];
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