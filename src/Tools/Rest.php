<?
/**
 * Manage Rest API request / response
 * 
 * @example
 * <u><b>Example :</b></u>
 * ```php
 * self::init();
 * self::checkParams(['id']);
 * try{
 *     self::renderSuccess();
 * } catch(Exception $e){
 *     self::renderError($e->getMessage());
 * }
 * ```
 */
class Rest
{
    /**
     * Request post data
     * @var array
     */
    public static array $params=[];
    /**
     * Current page number
     * @var int
     */
    public static int $currentPage = 1;
    /**
     * Limit of result by page
     * @var int
     */
    public static int $limit = 20;
    /**
     * Current count relative to the page and limit ($limit*$currentpage)
     * @var int
     */
    public static int $count = 0;
    /**
     * The total of the request page
     * @var int
     */
    public static int $nbPage = 1;

    /**
     * Check the conformity of the request and get the post data
     * 
     * @param null|bool $restrict=true Allow to disable the server name check restriction
     * 
     * @return void
     */
    public static function init(null|bool $restrict=true):void{
        if($restrict){
            self::checkReferer();
            self::checkRequest();
        }
        self::$params = $_REQUEST;
        $data = file_get_contents('php://input');
        if(isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json' && $data !== false && $data !== null && $data !== ""){
            self::$params += json_decode($data, true);
        }
        unset(self::$params['_url']);
        self::$currentPage = isset(self::$params['current_page']) ? (int)self::$params['current_page'] : self::$currentPage ;
        self::$limit = isset(self::$params['limit']) ? (int)self::$params['limit'] : self::$limit ;
        unset(self::$params['limit']);
        unset(self::$params['current_page']);
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
     * 
     * @return void
     */
    public static function checkRequest():void{
        if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest'){
            self::renderError("Restricted Access : Only XMLHttpRequest accepted !");
        }
    }

    /**
     * Check if the referer corresponding to the same server 
     * 
     * @return void
    */
    public static function checkReferer():void{
        $request = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $request = str_replace('/api/', '/', substr($request, 0, strrpos($request, '/')));
        if(APP === "frontend" && strpos($_SERVER['HTTP_REFERER'], 'http://'.$_SERVER["SERVER_NAME"]) === false && strpos($_SERVER['HTTP_REFERER'], 'https://'.$_SERVER["SERVER_NAME"]) === false){
            self::renderError("Restricted Access : Invalid Referer !");
        }
    }

    /**
     * Rest response on error
     * 
     * @param mixed $error='' Data corresponding to the error, can be any type
     * 
     * @return void
     */
    public static function renderError(mixed $error=''):void{
        self::render(false, ['error'=>$error]);
    }

    /**
     * Rest response on success
     * 
     * @param mixed $data='' Data render on success, can be any type
     * 
     * @return void
     */
    public static function renderSuccess(mixed $data=''):void{
        self::render(true, ['data'=>$data]);
    }

    /**
     * Rest response
     * 
     * @param null|array $params=[] Data to render
     * 
     * @return void
     */
    public static function renderJson(null|array $params=[]):void{
        header('Content-Type: application/json');
        die(json_encode($params));
    }

    /**
     * Normalize data to render
     * 
     * @param bool $result Specify is the response is on success or error
     * @param null|array $data=[] Data to render
     * 
     * @return void
     */
    public static function render(bool $result, null|array $data=[]):void{
        $data['success'] = $result;
        self::renderJson($data);
    }

    /**
     * Check the presence of required params
     * 
     * @param null|array $list=[] List of params name to check
     * @param null|bool $allowEmpty=false Set to true if data can be empty
     * 
     * @return void
     */
    public static function checkParams(null|array $list=[], null|bool $allowEmpty=false):void{
        if(!$allowEmpty && count(self::$params) === 0){
            self::renderError("No data found !");
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