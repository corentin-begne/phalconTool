<?

namespace Phalcon\Builder;

use Phalcon\Db\Column, Phalcon\Db;
use Phalcon\Text as Utils;
class Model extends \Phalcon\Mvc\User\Component
{
    public $constraints = [];

    private function getInfos($table, &$fields='', &$maps=array()){
        $modelField = 
"\n\t/**
\t * @[setting]])
\t */
\tpublic [name];\n";   
        // fields and map
        foreach($this->db->fetchAll('desc '.$table, Db::FETCH_ASSOC) as &$field){
            $setting = $this->getPrefix($table)."_".$field['Field'].'([';
            $type = $field['Type'];
            if(strpos($field['Type'], '(') !== false){
                $type = substr($field['Type'], 0, strpos($field['Type'], '('));
                $length = substr($field['Type'], strpos($field['Type'], '(')+1);
                $length = substr($length, 0, strpos($length, ')'));
                if(strpos($length, ',') !== false){
                    $length = substr($length, 0, strpos($length, ','));
                }
            }         
            $setting .= "'type':'".$type."'";
            $setting .= ', \'isNull\': '.(($field['Null'] === 'NO') ? "false" : "true");
            $setting .= (!empty($field['Default'])) ? ', \'default\': \''.$field['Default'].'\'' : '';
            $setting .= (!empty($field['Extra'])) ? ', \'extra\': \''.$field['Extra'].'\'' : '';
            $setting .= (!empty($field['Key'])) ? ', \'key\': \''.$field['Key'].'\'' : '';
            $setting .= (isset($length)) ? ', \'length\': '.$length : '';
            $fields .= str_replace(['[setting]', '[name]'], [$setting, '$'.$field['Field']], $modelField);
            $maps[] = "'".$field['Field']."' => '".$this->getPrefix($table)."_".$field['Field']."'";
        }
        $maps = implode(",\n\t\t\t", $maps);
    }

    private function getPrefix($table){
        $prefix = '';
        foreach(explode('_', Utils::uncamelize($table)) as $name){
            $prefix .= $name[0].$name[1];
        }
        return $prefix;
    }

    private function getConstraints($table, &$constraints=''){
        $modelConstraint =
"\t\t\$this->[type]('[local]', '[model]', '[foreign]', array('alias' => '[table]'));\n";        
        foreach($this->db->describeReferences($table, $this->config[ENV]->database->dbname) as &$reference){
            $foreignTable = $reference->getReferencedTable();                        
            $local = $this->getPrefix($table).'_'.$reference->getColumns()[0];
            $foreign = $this->getPrefix($foreignTable).'_'.$reference->getReferencedColumns()[0];           
            $fieldDesc = $this->db->fetchOne('show columns from '.$table.' where Field = \''.$reference->getColumns()[0] .'\'', Db::FETCH_ASSOC);
            $model = Utils::camelize(Utils::uncamelize($foreignTable));
            $constraints .= str_replace([
                '[type]',
                '[local]',
                '[model]',
                '[foreign]',
                '[table]',
            ], [
                'belongsTo',
                $local,
                $model,
                $foreign,
                Utils::uncamelize($foreignTable).'_'.$reference->getColumns()[0]
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
                '[table]',
            ], [
                $type,
                $foreign,
                Utils::camelize(Utils::uncamelize($table)),
                $local,
                Utils::uncamelize($table).'_'.$reference->getColumns()[0]
            ], $modelConstraint);
        }
    }

    public function __construct($table)
    {
        $source = file_get_contents(TEMPLATE_PATH.'/php/model.php');
        $this->getInfos($table, $fields, $maps);
        $this->getConstraints($table, $constraints);
        $name = Utils::camelize(Utils::uncamelize($table));
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
            $source);
        $target = $this->config->application->modelsDir.$name.'.php';
     //   if(!file_exists($target)){
            file_put_contents($target, $content);
            echo $target."\n";
     //   }
    }

}
