<?
namespace Phalcon\Tools;

/**
 * Tool coloring cli text and background, using green (success), red (error), orange (warning) and black
 */
class Cli{
    /**
     * Red background color
     * @var int
     */
    const ERROR = 41;
    /**
     * Orange background color
     * @var int
     */
    const WARNING = 43;
    /**
     * Green background color
     * @var int
     */
    const SUCCESS = 42;
    /**
     * Green text color
     * @var string
     */
    const GREEN = '0;32';
    /**
     * Red text color
     * @var string
     */
    const RED = '0;31';
    /**
     * Orange text color
     * @var string
     */
    const ORANGE = '0;33';
    /**
     * Black text color
     * @var string
     */
    const BLACK = '0;30';

    /**
     * Set a background color on a text using black text color
     * 
     * @param string $string Text to display
     * @param int $color Background color
     * 
     * @return string Text with color bakcground
     */
    public static function colorBackgroundString(string $string, int $color):string{
        return "\033[".self::BLACK.';'.$color.'m'.$string."\033[0m"; 
    }

    /**
     * Set a color on a text
     * 
     * @param string $string Text to display
     * @param null|string $color=\Phalcon\Tools\Cli::GREEN Text color
     * 
     * @return string Text with color
     */
    public static function colorString(string $string, null|string $color=self::GREEN):string{
        return "\033[".$color."m".$string."\033[0m"; 
    }

    /**
     * Display text in red and stop the program
     * 
     * @param string $string Text to display
     * 
     * @return void 
     */
    public static function error(string $string):void{
        echo self::colorString($string, self::RED);
        die;
    }

    /**
     * Display text in green with carriage return or not
     * 
     * @param string $string Text to display
     * @param null|bool $return=false Add a carriage return or not
     * 
     * @return void
     */
    public static function success(string $string, null|bool $return=false):void{
        echo self::colorString($string, self::GREEN);
        if($return){
            echo "\n";
        }
    }

    /**
     * Display text in orange with carriage return or not
     * 
     * @param string $string Text to display
     * @param null|bool $return=false Add a carriage return or not
     * 
     * @return void
     */
    public static function warning(string $string, null|bool $return=false):void{
        echo self::colorString($string, self::ORANGE);
        if($return){
            echo "\n";
        }
    }

    /**
     * Display text in black with red background and stop the program
     * 
     * @param string $string Text to display
     * 
     * @return void
     */
    public static function errorBg(string $string):void{
        echo self::colorBackgroundString($string, self::ERROR);
        die;
    }

    /**
     * Display text in black and green background with carriage return or not
     * 
     * @param string $string Text to display
     * @param null|bool $return=false Add a carriage return or not
     * 
     * @return void
     */
    public static function successBg(string $string, null|bool $return=false):void{
        echo self::colorBackgroundString($string, self::SUCCESS);
        if($return){
            echo "\n";
        }
    }

    /**
     * Display text in black and orange background with carriage return or not
     * 
     * @param string $string Text to display
     * @param null|bool $return=false Add a carriage return or not
     * 
     * @return void
     */
    public static function warningBg(string $string, null|bool $return=false):void{
        echo self::colorBackgroundString($string, self::WARNING);
        if($return){
            echo "\n";
        }
    }
}

?>