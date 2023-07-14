<?

namespace Phalcon\Builder;

use Phalcon\Support\Helper\Str\Camelize,
Phalcon\Support\Helper\Str\Uncamelize,
Phalcon\DI\Injectable,
Phalcon\Db\Enum;

/**
 * Manage models generation
 */
class Model extends Injectable
{    
    /**
     * Model constraints
     * @var array
     */
    private array $constraints = [];
    /**
     * Object to camelize texts
     * @var \Phalcon\Support\Helper\Str\Camelize
     */
    private Camelize $camelize;
    /**
     * Object to uncamelize texts
     * @var \Phalcon\Support\Helper\Str\Uncamelize
     */
    private Uncamelize $uncamelize;
    /**
     * Object to get models utils
     * @var \Phalcon\Builder\Utils
     */
    private Utils $utils;

    /**
     * Generate model
     * 
     * @param string $table Name of the model
     */
    public function __construct(string $table)
    {
        $this->utils = new Utils();
        $this->camelize = new Camelize();
        $this->uncamelize = new Uncamelize();
        $source = file_get_contents(TEMPLATE_PATH.'/php/model.php');
        $this->getInfos($table, $fields, $maps);
        $this->getConstraints($table, $constraints);
        $name = ($this->camelize)(($this->uncamelize)($table));
        $content = str_replace([
            '[fields]', 
            '[name]', 
            '[realName]', 
            '[constraints]',
            '[maps]'
        ], [
            $fields, 
            $name, 
            $table, 
            $constraints,
            $maps], 
            $source            
        );
        $target = $this->config->application->modelsDir.$name.'.php';
        file_put_contents($target, $content);
        echo $target."\n";
    }

    /**
     * Get model fields and array map
     * 
     * @param string Table name
     * @param null|string &$fields='' Fields content for model file
     * 
     * @return void
     */
    private function getInfos(string $table, null|string &$fields='', null|array &$maps=[]):void{
        $modelField = 
"\n\t/**[infos]
\t * @Column([setting])
\t */
\tpublic [name];\n";   
        // fields
        foreach($this->db->fetchAll('desc '.$table, Enum::FETCH_ASSOC) as &$field){
            $infos = '';
            $setting = 'column=\''.$field['Field'].'\'';
            $type = $field['Type'];
            $length = null;
            if($field['Key'] === 'PRI'){
                $infos .= "\n\t * @Primary";
            }
            if($field['Extra'] === 'auto_increment'){
                $infos .= "\n\t * @Identity";
            }
            if(strpos($field['Type'], '(') !== false){
                $type = substr($field['Type'], 0, strpos($field['Type'], '('));
                $length = substr($field['Type'], strpos($field['Type'], '(')+1);
                $length = substr($length, 0, strpos($length, ')'));
                if(strpos($length, ',') !== false && !in_array($type, ['set', 'enum'])){
                    $length = substr($length, 0, strpos($length, ','));
                }
            }         
            
            $setting .= ", type='".$this->utils->mysqlToPhalconTypes[$type]."'";
            $setting .= ", mtype='".$type."'";
            $setting .= ', nullable='.(($field['Null'] === 'NO') ? 'false' : 'true');
            $setting .= (!empty($field['Default'])) ? ', default=\''.$field['Default'].'\'' : '';
            $setting .= (!empty($field['Extra'])) ? ', extra=\''.$field['Extra'].'\'' : '';
            $setting .= (!empty($field['Key'])) ? ', key=\''.$field['Key'].'\'' : '';
            if(isset($field['Key'])){
                $constraint = $this->db->fetchOne('SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA =  \''.$this->config[ENV]->database->dbname.'\' AND TABLE_NAME =  \''.$table.'\' and COLUMN_NAME=\''.$field['Field'].'\'', \Phalcon\Db\Enum::FETCH_ASSOC);
                if($constraint !== false){
                    $constraint2 = $this->db->fetchOne('SELECT * FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA =  \''.$this->config[ENV]->database->dbname.'\' AND TABLE_NAME =  \''.$table.'\' and REFERENCED_TABLE_NAME=\''.$constraint['REFERENCED_TABLE_NAME'].'\'', \Phalcon\Db\Enum::FETCH_ASSOC);
                    $setting .= ', \'onUpdate\': \''.$constraint2['UPDATE_RULE'].'\', \'onDelete\': \''.$constraint2['DELETE_RULE'].'\'';
                }
            }            
            $setting .= (isset($length)) ? ', \'length\': '.(in_array($type, ['set', 'enum']) ? '\'' : '').str_replace('\'', '', $length).(in_array($type, ['set', 'enum']) ? '\'' : '') : '';
            $fields .= str_replace(['[infos]', '[setting]', '[name]'], [$infos, $setting, '$'.$this->getPrefix($table).'_'.$field['Field']], $modelField);
            $maps[] = "'".$field['Field']."' => '".$this->getPrefix($table)."_".$field['Field']."'";           
        }
        $maps = implode(",\n\t\t\t", $maps);
    }

    /**
     * Get table prefix
     * 
     * @param string $table Table name
     * 
     * @return string Prefix of the table
     */
    private function getPrefix(string $table):string{
        $prefix = '';
        foreach(explode('_', ($this->uncamelize)($table)) as $name){
            $prefix .= $name[0].$name[1];
        }
        return $prefix;
    }

    /**
     * Get table constraints
     * 
     * @param string $table Table name
     * @param null|string &$constraints='' Table constraints
     * 
     * @return void
     */
    private function getConstraints(string $table, null|string &$constraints=''):void{
        $modelConstraint =
"\t\t\$this->[type]('[local]', '[model]', '[foreign]', array('alias' => '[table]'));\n";        
        foreach($this->db->describeReferences($table, $this->config[ENV]->database->dbname) as &$reference){
            $foreignTable = $reference->getReferencedTable();                        
            $local = $this->getPrefix($table).'_'.$reference->getColumns()[0];
            $foreign = $this->getPrefix($foreignTable).'_'.$reference->getReferencedColumns()[0];           
            $fieldDesc = $this->db->fetchOne('show columns from '.$table.' where Field = \''.$reference->getColumns()[0] .'\'', \Phalcon\Db\Enum::FETCH_ASSOC);
            $model = ($this->camelize)(($this->uncamelize)($foreignTable));
            $constraints .= str_replace([
                '[type]',
                '[local]',
                '[model]',
                '[foreign]',
                '[table]'
            ], [
                'belongsTo',
                $local,
                $model,
                $foreign,
                ($this->uncamelize)($foreignTable).'_'.$reference->getColumns()[0]
            ], $modelConstraint);
            if(!isset($this->constraints[$model])){
                $this->constraints[$model] = [];
            }
            if($fieldDesc['Key'] === 'UNI'){
                $type = 'hasOne';
            } else {
                $type = 'hasMany';
            }
            $this->constraints[$model][] = str_replace([
                '[type]',
                '[local]',
                '[model]',
                '[foreign]',
                '[table]'
            ], [
                $type,
                $foreign,
                ($this->camelize)(($this->uncamelize)($table)),
                $local,
                ($this->uncamelize)($table).'_'.$reference->getColumns()[0]
            ], $modelConstraint);
        }
    }

}
