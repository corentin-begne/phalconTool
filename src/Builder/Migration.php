<?

namespace Phalcon\Builder;
use Phalcon\Di\Di,
Phalcon\Tools\Cli,
Phalcon\Db\Enum,
Phalcon\DI\Injectable,
Phalcon\Support\Helper\Str\Camelize,
Phalcon\Support\Helper\Str\Uncamelize;

/**
 * Manage migrations generation
 */
class Migration extends Injectable
{
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
     * Generate migration
     */
    public function __construct(){
        $this->utils = new Utils();
        $this->camelize = new Camelize();
        $this->uncamelize = new Uncamelize();
        $modelVersion = '<? 
    class MigrationVersion[nb]{
        public function up(){
            return [up];
        }
        public function down(){
            return [down];
        }
    }
?>';        
        $definitions = [
            'tables'=>[
                'create'=>[], 
                'drop'=>[]
            ], 
            'fields'=>[
                'add'=>[],
                'drop'=>[],
                'modify'=>[]
            ], 
            'indexes'=>[
                'add'=>[],
                'drop'=>[]
            ], 
            'uniques'=>[
                'add'=>[],
                'drop'=>[]
            ], 
            'keys'=>[ 
                'foreign'=>[
                    'add'=>[],
                    'drop'=>[]
                ],
                'primary'=>[
                    'add'=>[],
                    'drop'=>[]
                ]
            ]
        ];
        $modelActionTpl = ['up'=>$definitions, 'down'=>$definitions];
        $migration = [];
        // generate migration difference between models and bdd
        $models = glob($this->config->application->modelsDir.'*.php');
        if(count($models) === 0){
            Cli::error('No models to check');
        }
        // check tables
        foreach($this->db->listTables($this->config[ENV]->database->dbname) as $table){
            $sourceModel = ($this->camelize)(($this->uncamelize)($table));
            if(!class_exists("\\$sourceModel")){                
                $modelActionTpl['up']['tables']['drop'][] = $table;
                $modelActionTpl['down']['tables']['create'][] = $table;
                // get all fields, constraints, indexes, uniques for the rollback
                $modelActionTpl['down']['fields']['add'][$table] = [];
                foreach($this->db->fetchAll('show columns from '.$table, Enum::FETCH_ASSOC) as $field){
                    $modelActionTpl['down']['fields']['add'][$table][$field['Field']] = $this->normalize($field);
                    switch($field['Key']){
                        case 'PRI':
                            $modelActionTpl['down']['keys']['primary']['add'][$table] = $field['Key'];
                            break;
                        case 'UNI':
                            if(!isset($modelActionTpl['down']['uniques']['add'][$table])){
                                $modelActionTpl['down']['uniques']['add'][$table] =[];
                            }
                            $modelActionTpl['down']['uniques']['add'][$table][] = $field['Key'];
                            break;
                        case 'MUL':
                            $constraint = $this->db->fetchOne('SELECT * FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA =  \''.$this->config[ENV]->database->dbname.'\' AND TABLE_NAME =  \''.$table.'\' and COLUMN_NAME=\''.$field['Field'].'\'', Enum::FETCH_ASSOC);
                            if(!$constraint){
                                if(!isset($modelActionTpl['down']['indexes']['add'][$table])){
                                    $modelActionTpl['down']['indexes']['add'][$table] =[];
                                }
                                $modelActionTpl['down']['indexes']['add'][$table][] = $field['Key'];
                            } else {                               
                                if(!isset($modelActionTpl['down']['keys']['foreign']['add'][$table])){
                                    $modelActionTpl['down']['keys']['foreign']['add'][$table] = [];
                                }
                                $modelActionTpl['down']['keys']['foreign']['add'][$table][] = [
                                    'column'=>$field['Field'],
                                    'referenced_column'=>$constraint['REFERENCED_COLUMN_NAME'],
                                    'referenced_table'=>$constraint['REFERENCED_TABLE_NAME'],
                                    'onUpdate'=>$constraint['UPDATE_RULE'],
                                    'onDelete'=>$constraint['DELETE_RULE']
                                ];
                            }
                            break;
                    }
                }
            }
        }
        // check fields
        foreach($models as $model){
            $model = basename($model, '.php');
            $source = new $model(); 
            $sourceModel = $source->getSource();
            $action = 'update';
            $tableExists = $this->db->tableExists($sourceModel);
            if(!$tableExists){
                $modelActionTpl['up']['tables']['create'][] = $sourceModel;
                $modelActionTpl['down']['tables']['drop'][] = $sourceModel;
                $action = 'create';
            }
            $modelActionTpl['up']['fields']['add'][$sourceModel] = [];
            $modelActionTpl['up']['fields']['drop'][$sourceModel] = [];
            $modelActionTpl['up']['fields']['modify'][$sourceModel] = [];
            if(!isset($modelActionTpl['down']['fields']['add'][$sourceModel])){
                $modelActionTpl['down']['fields']['add'][$sourceModel] = [];
            }
            $modelActionTpl['down']['fields']['drop'][$sourceModel] = [];
            $modelActionTpl['down']['fields']['modify'][$sourceModel] = [];
            // check fields, get annotations     
            $fields = $model::getColumnsDescription();
        //    $columns = $source->getColumnsMap();    
            if($tableExists){
                $primary = $this->db->fetchOne('SHOW KEYS FROM '.$sourceModel.' WHERE Key_name = \'PRIMARY\'');
                $indexes = $this->db->fetchALL('SHOW columns FROM '.$sourceModel);
                if($indexes !== false){
                    foreach($indexes as $index){
                        foreach($fields as $field => &$fieldAnnotation) { 
                            if($index['Field'] === $fieldAnnotation['column'] && isset($fieldAnnotation['key']) && $fieldAnnotation['key'] !== $index['Key']){
                                $modelActionTpl['up'][$index['Key'] === 'UNI' ? 'uniques' : 'indexes']['drop'][$sourceModel][] = $fieldAnnotation['column'];
                                $modelActionTpl['down'][$index['Key'] === 'UNI' ? 'uniques' : 'indexes']['add'][$sourceModel][] = $fieldAnnotation['column'];
                                $modelActionTpl['up'][$index['Key'] !== 'UNI' ? 'uniques' : 'indexes']['add'][$sourceModel][] = $fieldAnnotation['column'];
                                $modelActionTpl['down'][$index['Key'] !== 'UNI' ? 'uniques' : 'indexes']['drop'][$sourceModel][] = $fieldAnnotation['column'];
                            }
                        }
                    }
                }
            }
            foreach($fields as $field => &$fieldAnnotation) {
                $field = $fieldAnnotation['column'];  
                // check relations         
                if($action === 'update'){                       
                    $fieldDesc = $this->db->fetchOne('show columns from '.$sourceModel.' where Field = \''.$field .'\'', Enum::FETCH_ASSOC);
                    if(!$fieldDesc){
                        $modelActionTpl['up']['fields']['add'][$sourceModel][$field] = $fieldAnnotation;
                        $modelActionTpl['down']['fields']['drop'][$sourceModel][$field] = '';
                    }else if(!$this->checkField($fieldAnnotation, $fieldDesc)){             
                        $modelActionTpl['up']['fields']['modify'][$sourceModel][$field] = $fieldAnnotation;
                        $modelActionTpl['down']['fields']['modify'][$sourceModel][$field] = $fieldDesc;                    
                    }               
                    if(isset($fieldAnnotation['key'])){
                        $indexName = 'indexes';
                        if($primary !== false && $primary['Column_name'] === $field && $fieldAnnotation['key'] !== 'PRI'){
                            $modelActionTpl['up']['keys']['primary']['drop'][$sourceModel][] = $field;
                            $modelActionTpl['down']['keys']['primary']['add'][$sourceModel][] = $field;
                        }
                        switch($fieldAnnotation['key']){
                            case 'PRI':                                
                                if($primary !== false && $primary['Column_name'] !== $field){
                                    $modelActionTpl['up']['keys']['primary']['add'][$sourceModel][] = $field;
                                    $modelActionTpl['down']['keys']['primary']['drop'][$sourceModel][] = $field;
                                }
                                break;
                            case 'UNI':
                                $indexName = 'unique';
                            default:                                
                                $haveIndex = false;
                                if($indexes !== false){
                                    foreach($indexes as $index){
                                        if($index['Field'] === $field){
                                            $haveIndex = true;
                                            break;
                                        }
                                    }
                                }
                                if(!$haveIndex){
                                    if(!isset($modelActionTpl['up'][$indexName]['add'][$sourceModel])){
                                        $modelActionTpl['up'][$indexName]['add'][$sourceModel] = [];
                                        $modelActionTpl['up'][$indexName]['drop'][$sourceModel] = [];
                                    }
                                    $modelActionTpl['up'][$indexName]['add'][$sourceModel][] = $field;
                                    $modelActionTpl['down'][$indexName]['drop'][$sourceModel][] = $field;
                                }
                                break;
                        }
                    } else if($primary !== false && $primary['Column_name'] === $field){
                        $modelActionTpl['up']['keys']['primary']['drop'][$sourceModel][] = $field;
                            $modelActionTpl['down']['keys']['primary']['add'][$sourceModel][] = $field;
                    }
                } else {
                    $modelActionTpl['up']['fields']['add'][$sourceModel][$field] = $fieldAnnotation;
                    $modelActionTpl['down']['fields']['drop'][$sourceModel][$field] = '';
                    if(isset($fieldAnnotation['key'])){
                        $indexName = 'indexes';
                        switch($fieldAnnotation['key']){
                            case 'PRI':
                                $modelActionTpl['up']['keys']['primary']['add'][$sourceModel][] = $field;
                                $modelActionTpl['down']['keys']['primary']['drop'][$sourceModel][] = $field;
                                break;
                            case 'UNI':
                                $indexName = 'unique';
                            default:
                                if(!isset($modelActionTpl['up'][$indexName]['add'][$sourceModel])){
                                    $modelActionTpl['up'][$indexName]['add'][$sourceModel] = [];
                                    $modelActionTpl['up'][$indexName]['drop'][$sourceModel] = [];
                                }
                                $modelActionTpl['up'][$indexName]['add'][$sourceModel][] = $field;
                                $modelActionTpl['down'][$indexName]['drop'][$sourceModel][] = $field;
                                break;
                        }
                    }
                }                               
            }

            if($action === 'update'){
                // now need to check the inverse to drop which are removed
                foreach($this->db->fetchAll('show columns from '.$sourceModel, Enum::FETCH_ASSOC) as $fieldDesc){

                    if(!isset($fields[$this->getPrefix($sourceModel).'_'.$fieldDesc['Field']])){
                        $modelActionTpl['up']['fields']['drop'][$sourceModel][$fieldDesc['Field']] = '';
                        $modelActionTpl['down']['fields']['add'][$sourceModel][$fieldDesc['Field']] = $this->normalize($fieldDesc);
                    }
                }    
            }    
            // check constraints
            if(!isset($modelActionTpl['up']['keys']['foreign']['add'][$sourceModel])){
                $modelActionTpl['up']['keys']['foreign']['add'][$sourceModel] = [];
                $modelActionTpl['up']['keys']['foreign']['drop'][$sourceModel] = [];
                $modelActionTpl['down']['keys']['foreign']['add'][$sourceModel] = [];
                $modelActionTpl['down']['keys']['foreign']['drop'][$sourceModel] = [];
            }              
            $constraints = $this->db->fetchAll('SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA =  \''.$this->config[ENV]->database->dbname.'\' AND TABLE_NAME =  \''.$sourceModel.'\'', Enum::FETCH_ASSOC);
            $mtpm = new $sourceModel();
            $relations = $mtpm->getModelsManager()->getRelations($sourceModel);
            if($constraints !== false){
                foreach($constraints as $constraint){
                    $relationExists = false;
                    foreach($relations as $name => $relation){
                        if($fields[$relation->getFields()]['column'] === $constraint['COLUMN_NAME']){
                            $relationExists = true;
                            break;
                        }
                    }
                    if(!$relationExists){
                        $constraint2 = $this->db->fetchOne('SELECT * FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA =  \''.$this->config[ENV]->database->dbname.'\' AND TABLE_NAME =  \''.$sourceModel.'\' and REFERENCED_TABLE_NAME=\''.$constraint['REFERENCED_TABLE_NAME'].'\'', Enum::FETCH_ASSOC);
                        $modelActionTpl['up']['keys']['foreign']['drop'][$sourceModel][]= $constraint2['CONSTRAINT_NAME'];
                        $modelActionTpl['down']['keys']['foreign']['add'][$sourceModel][]= [
                            'column'=>$constraint['COLUMN_NAME'],
                            'referenced_column'=>$constraint['REFERENCED_COLUMN_NAME'],
                            'referenced_table'=>$constraint['REFERENCED_TABLE_NAME'],
                            'onUpdate'=>$constraint2['UPDATE_RULE'],
                            'onDelete'=>$constraint2['DELETE_RULE']
                        ];
                    }
                }
            }

            foreach(['hasOne', 'belongsTo'] as $type){
                $relations = $source->returnRelations($type);
                foreach($relations as $name => $relation){                    
                    $constraint = $this->db->fetchOne('SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA =  \''.$this->config[ENV]->database->dbname.'\' AND TABLE_NAME =  \''.$sourceModel.'\' and COLUMN_NAME=\''.$fields[$name]['column'].'\'', Enum::FETCH_ASSOC);
                    if($constraint !== false){                    
                        $constraint2 = $this->db->fetchOne('SELECT * FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA =  \''.$this->config[ENV]->database->dbname.'\' AND TABLE_NAME =  \''.$sourceModel.'\' and REFERENCED_TABLE_NAME=\''.$constraint['REFERENCED_TABLE_NAME'].'\'', Enum::FETCH_ASSOC);
                        if(!$constraint2 || $constraint2['UPDATE_RULE'] !== $fields[$name]['onUpdate'] || $constraint2['DELETE_RULE'] !== $fields[$name]['onDelete']){
                            $modelActionTpl['up']['keys']['foreign']['add'][$sourceModel][] = [
                                'column'=>$fields[$name]['column'],
                                'referenced_column'=>$constraint['REFERENCED_COLUMN_NAME'],
                                'referenced_table'=>$constraint['REFERENCED_TABLE_NAME'],
                                'onUpdate'=>$fields[$name]['onUpdate'],
                                'onDelete'=>$fields[$name]['onDelete']
                            ];
                            $modelActionTpl['up']['keys']['foreign']['drop'][$sourceModel][]= $fields[$name]['column'];
                            $modelActionTpl['down']['keys']['foreign']['drop'][$sourceModel][]= $fields[$name]['column'];
                            if($constraint2 !== false){
                                $modelActionTpl['down']['keys']['foreign']['add'][$sourceModel][] = [
                                    'column'=>$fields[$name]['column'],
                                    'referenced_column'=>$constraint['REFERENCED_COLUMN_NAME'],
                                    'referenced_table'=>$constraint['REFERENCED_TABLE_NAME'],
                                    'onUpdate'=>$constraint2['UPDATE_RULE'],
                                    'onDelete'=>$constraint2['DELETE_RULE']
                                ];
                            }
                        }
                    } else {
                        // add foreign key                        
                        $modelActionTpl['up']['keys']['foreign']['add'][$sourceModel][] = [
                            'column'=>$fields[$name]['column'],
                            'referenced_column'=>substr($relation['field'], strpos($relation['field'], '_')+1),
                            'referenced_table'=>(new $relation['model']())->getSource(),
                            'onUpdate'=>$fields[$name]['onUpdate'],
                            'onDelete'=>$fields[$name]['onDelete']
                        ];
                        $modelActionTpl['down']['keys']['foreign']['drop'][$sourceModel][]= $fields[$name]['column'];
                    }                  
                }
            } 
        }     
        $migrations = glob($this->config->application->migrationsDir.'*.php');
        if(self::getCurrentVersion()<count($migrations)){
            Cli::error('There\'s already a migration to run');
        } else if($this->isArrayEmpty($modelActionTpl)){
            Cli::error('No migration to generate');
        } else {
            $nb = (count($migrations)+1);
            // check empty array
            $this->removeEmptyArray($modelActionTpl);
            $up = var_export($modelActionTpl['up'], true);
            $down = var_export($modelActionTpl['down'], true);
            file_put_contents($this->config->application->migrationsDir.'MigrationVersion'.$nb.'.php', str_replace([
                '[nb]',
                '[up]',
                '[down]',
            ], [
                $nb,
                $up,
                $down
            ], $modelVersion));
        }        
    }

    /**
     * Clean data removing empty arrays
     * @param array &$data data to clean
     * 
     * @return void
     */
    private function removeEmptyArray(array &$data):void{
        foreach($data as $tName => &$types){
            foreach($types as $aName => &$actions){
                if($aName ==='tables'){
                    foreach($actions as $taName =>&$table){
                        if(count($table) === 0){
                            unset($data[$tName][$aName][$taName]);
                        }
                    }
                    if(count($data[$tName][$aName]) === 0){
                        unset($data[$tName][$aName]);
                    }
                } else {// if(in_array($tName, ['fields', 'indexes', 'uniques'])){
                    foreach($actions as $taName =>&$table){
                        foreach($table as $taName2 =>&$table2){
                            if(count($table2) === 0){
                                unset($data[$tName][$aName][$taName][$taName2]);
                            } else if(is_array($table2)){
                                foreach($table2 as $taName3 =>&$table3){
                                    if(is_array($table3) && count($table3) === 0){
                                        unset($data[$tName][$aName][$taName][$taName2][$taName3]);
                                    }
                                }
                                if(count($table2) === 0){
                                    unset($data[$tName][$aName][$taName][$taName2]);
                                }
                            }
                        }
                        if(count($data[$tName][$aName][$taName]) === 0){
                            unset($data[$tName][$aName][$taName]);
                        }
                    }
                    if(count($data[$tName][$aName]) === 0){
                        unset($data[$tName][$aName]);
                    }
                }
            }
        }
    }

    /**
     * Check recursivly if there's an empty array
     * 
     * @param array|string $data Data to check
     * 
     * @return bool Result of the check
     */
    private function isArrayEmpty(array|string $data):bool{
        if(is_array($data)){
            foreach($data as $value){
                if(!$this->isArrayEmpty($value)) {
                    return false;
                }
            }
        } else if(!empty($data)){
            return false;
        }

        return true;
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
     * Normalize field info from database
     * 
     * @param array $field Field data to normalize
     * 
     * @return array Field data normalized
     */
    public function normalize(array $field):array{
        $type = $field['Type'];
        if(strpos($field['Type'], '(') !== false){
            $type = substr($field['Type'], 0, strpos($field['Type'], '('));
            $length = substr($field['Type'], strpos($field['Type'], '(')+1);
            $length = substr($length, 0, strpos($length, ')'));
            if(strpos($length, ',') !== false){
                $length = substr($length, 0, strpos($length, ','));
            }
        }
        $data = [
            'mtype' => $type,
            'nullable' => ($field['Null'] === 'NO') ? false : true,
        ];
        if(!empty($field['Default'])){
            $data['default'] = $field['Default'];
        }
        if(!empty($field['Extra']) && $field['Extra'] !== 'DEFAULT_GENERATED'){
            $data['extra'] = $field['Extra'];
        }
        if(!empty($field['Key'])){
            $data['key'] = $field['Key'];
        }
        if(isset($length)){
            $data['length'] = $length;
        }
        return $data;
    }

    /**
     * Check diffrence betwen 2 fields
     * 
     * @param array $new New field
     * @param array &$old Old field
     * 
     * @return bool Result of the check
     */
    public function checkField(array $new, array &$old):bool{
        unset($new['onUpdate']);
        unset($new['onDelete']);
        unset($new['key']);
        unset($new['type']);
        unset($new['column']);
        $old = $this->normalize($old);    
        $old2 = $old;
        unset($old2['key']);
        if(count(array_diff($new ,$old2))>0){
            return false;
        }
        return true;
    }

    /**
     * Get current migration version
     * 
     * @return int Migration version
     */
    public static function getCurrentVersion():int{
        $file = DI::getDefault()->get('config')->application->migrationsDir.ENV.'_version';
        if(!file_exists($file)){
            self::setCurrentVersion(0);
        }
        return (int)file_get_contents($file);
    }

    /**
     * Set a migration version
     * 
     * @param int $version Version to set
     * 
     * @return int|false Result of file_put_contents
     */
    public static function setCurrentVersion(int $version):int|false{
        return file_put_contents(DI::getDefault()->get('config')->application->migrationsDir.ENV.'_version', $version);
    }

}