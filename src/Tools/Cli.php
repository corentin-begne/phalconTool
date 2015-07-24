<?
namespace Phalcon\Tools;
class Cli{
    const ERROR = 41;
    const WARNING = 43;
    const SUCCESS = 42;
    const GREEN = "0;32";
    const BLACK = "0;30";

    public static function colorBackgroundString($string, $color){
        return "\033[".self::BLACK."m\033[".$color."m".$string."\033[0m"; 
    }

    public static function colorString($string, $color=self::GREEN){
        return "\033[".$color."m".$string."\033[0m"; 
    }

    public static function error($string){
        echo self::colorBackgroundString($string, self::ERROR);
        die;
    }
    public static function success($string, $return=false){
        echo self::colorBackgroundString($string, self::SUCCESS);
        if($return){
            echo "\n";
        }
    }
    public static function warning($string, $return=false){
        echo self::colorBackgroundString($string, self::WARNING);
        if($return){
            echo "\n";
        }
    }
}

?>