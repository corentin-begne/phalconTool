<?

namespace Phalcon\Builder;

use Phalcon\DI\Injectable;

/**
 * Manage builders utils
 */
class Utils extends Injectable
{
    /**
     * Mysql types to phalcon
     * @var array
     */
    public array $mysqlToPhalconTypes = [
        'int' => 'integer',
        'tinyint' => 'integer',
        'smallint' => 'integer',
        'mediumint' => 'integer',
        'bigint' => 'integer',
        'float' => 'float',
        'double' => 'double',
        'decimal' => 'decimal',
        'char' => 'string',
        'varchar' => 'string',
        'text' => 'text',
        'mediumtext' => 'text',
        'longtext' => 'text',
        'date' => 'date',
        'datetime' => 'datetime',
        'timestamp' => 'datetime',
        'time' => 'time',
        'year' => 'integer'
    ];
    public array $phalconToMysqlTypes;

    /**
     * Init variables
     */
    public function __construct(){                
        $this->phalconToMysqlTypes = array_flip($this->mysqlToPhalconTypes);
    }
}