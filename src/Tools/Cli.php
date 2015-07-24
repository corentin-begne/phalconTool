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
        die(self::colorBackgroundString($string, self::ERROR));
    }
    public static function success($string){
        echo self::colorBackgroundString($string, self::SUCCESS);
    }
    public static function warning($string){
        echo self::colorBackgroundString($string, self::WARNING);
    }
}

?>