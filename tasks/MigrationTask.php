<?
use Phalcon\Tools\Cli,
Phalcon\Builder\Migration,
\Phalcon\CLI\Task;
/**
 * Task managing database migrations
 */
class MigrationTask extends Task
{

    /**
     * Main task action (not implemented)
     */
    public function mainAction():void{

    }

    /**
     * Execute migrations not already done based on env_version
     * 
     * @return void
     */
    public function runAction():void{
        $currentVersion = Migration::getCurrentVersion();
        $migrations = glob($this->config->application->migrationsDir.'*.php');
        $version = count($migrations);
        if((int)$version === $currentVersion){
            Cli::error('Nothing to migrate');
        }
        for($i=($currentVersion+1); $i<=$version; $i++){
            $class= "MigrationVersion".$i; 
            $migration = new $class();
            $this->executeQueries($migration->up());

            Migration::setCurrentVersion($i);
        }
    }

    /**
     * Generate a migration checking difference between models and database
     * 
     * @return void
     */
    public function generateAction():void{
        new Migration();
    }

    /**
     * Execute queries from migration data
     * 
     * @param array $data Migration data
     * 
     * @return void
     */
    private function executeQueries(array $data):void{
        if(isset($data['tables'])){
            foreach($data['tables'] as $action => $tables){
                foreach($tables as $table){
                    try{
                        $query = $action.' TABLE '.$table;
                        if($action === 'create'){
                            $query .= ' (';
                            foreach($data['fields']['add'][$table] as $name => $field){
                                $query .= $this->getFieldQuery($name, $field).',';
                            }
                            unset($data['fields']['add'][$table]);
                            if(isset($data['keys']) && isset($data['keys']['primary']) && isset($data['keys']['primary']['add']) && isset($data['keys']['primary']['add'][$table])){
                                $query .= 'PRIMARY KEY ('.$data['keys']['primary']['add'][$table][0].'),';
                                unset($data['keys']['primary']['add'][$table]);
                            }
                            if(isset($data['indexes']) && isset($data['indexes']['add']) && isset($data['indexes']['add'][$table])){
                                foreach($data['indexes']['add'][$table] as $field){
                                    $query .= 'KEY '.$field.' ('.$field.'),';
                                }
                                unset($data['indexes']['add'][$table]);
                            }
                            if(isset($data['uniques']) && $data['uniques']['add'] && $data['uniques']['add'][$table]){
                                foreach($data['uniques']['add'][$table] as $field){
                                    $query .= 'UNIQUE '.$field.' ('.$field.'),';
                                }
                                unset($data['uniques']['add'][$table]);
                            }
                            if(isset($data['keys']) && isset($data['keys']['foreign']) && isset($data['keys']['foreign']['add']) && isset($data['keys']['foreign']['add'][$table])){
                                foreach($data['keys']['foreign']['add'][$table] as $info){
                                    $query .= 'CONSTRAINT fk_'.$table.'_'.$info['column'].' FOREIGN KEY ('.$info['column'].') REFERENCES '.$info['referenced_table'].'('.$info['referenced_column'].') ON UPDATE '.$info['onUpdate'].' ON DELETE '.$info['onDelete'].',';

                                }
                                unset($data['keys']['foreign']['add'][$table]);
                            }
                            $query = trim($query, ',').')';
                        }
                        $this->db->execute($query);
                        Cli::success($query, true);
                    } catch(PDOException $e){
                        Cli::error($query."\n".$e->getMessage());
                    }
                }
            }
        }    
        if(isset($data['keys'])){   
            foreach(['primary', 'foreign'] as $type){
                if(isset($data['keys'][$type])){
                    foreach(['drop', 'add'] as $action){
                        if(isset($data['keys'][$type][$action])){
                            foreach($data['keys'][$type][$action] as $table => $fields){
                                foreach($fields as $field){             
                                    $query = 'ALTER TABLE '.$table.' '.strtoupper($action).' ';
                                    switch($type){
                                        case 'foreign':
                                            switch($action){
                                                case 'drop':
                                                    $query .= 'FOREIGN KEY '.$field;
                                                    break;
                                                case 'add':
                                                    $query .= 'CONSTRAINT fk_'.$table.'_'.$field['column'].' FOREIGN KEY ('.$field['column'].') REFERENCES '.$field['referenced_table'].'('.$field['referenced_column'].') ON UPDATE '.$field['onUpdate'].' ON DELETE '.$field['onDelete'];
                                                    break;
                                            }
                                            break;
                                        case 'primary':
                                            $query .= 'PRIMARY KEY'.($action === 'add' ? '('.$field.')' : '');
                                            break;
                                    }
                                    try{
                                        $this->db->execute('SET FOREIGN_KEY_CHECKS=0;'.$query.';SET FOREIGN_KEY_CHECKS=1;');
                                        Cli::success($query, true);
                                    } catch(PDOException $e){
                                        Cli::error($query."\n".$e->getMessage());
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach(['indexes', 'uniques'] as $type){
            foreach(['add', 'drop'] as $action){
                if(isset($data[$type]) && isset($data[$type][$action])){
                    foreach($data[$type][$action] as $table => $fields){
                        foreach($fields as $field){
                            switch($type){
                                case 'indexes':
                                    $query = 'ALTER TABLE '.$table.' '.strtoupper($action).' INDEX '.($action==='add' ? '('.$field.')' : $field);
                                    break;
                                case 'uniques':
                                    $query = 'ALTER TABLE '.$table.' '.strtoupper($action).' ';
                                    switch($action){
                                        case 'add':
                                            $query .= 'UNIQUE ('.$field.')';
                                            break;
                                        case 'drop':
                                            $query .= 'INDEX '.$field;
                                            break;
                                    }
                                    break;
                            }
                            try{
                                $this->db->execute('SET FOREIGN_KEY_CHECKS=0;'.$query.';SET FOREIGN_KEY_CHECKS=1;');
                                Cli::success($query, true);
                            } catch(PDOException $e){
                                Cli::error($query."\n".$e->getMessage());
                            }
                        }
                    }                    
                }
            }
        }

        if(isset($data['fields'])){
            foreach($data['fields'] as $action => &$tables){
                foreach($tables as $table => &$fields){
                    foreach($fields as $name => &$field){
                        try{
                            if(empty($field)){
                                $field = null;
                            }
                            $query = $this->createAlter($table, $action, $name, $field);
                            $this->db->execute($query);
                            Cli::success($query, true);
                        } catch(PDOException $e){
                            Cli::error($query."\n".$e->getMessage());
                        }
                    }
                }
            }
        }
    }

    /**
     * Generate alter query according to action
     * 
     * @param string $table Table name
     * @param string $action Alter action
     * @param string $name Field name
     * @param null|array $field=null Field informations, null for drop
     * 
     * @return string Alter query
     */
    private function createAlter(string $table, string $action, string $name, null|array $field=null):string{
        if($action === 'drop'){
            return 'ALTER TABLE '.$table.' DROP COLUMN '.$name;
        } else {
            return 'ALTER TABLE '.$table.' '.$action.' '.$this->getFieldQuery($name, $field);
        }
    }

    /**
     * Create part of alter table corresponding to the field information
     * 
     * @param string $name Field name
     * @param array $field Field informations
     * 
     * @return string Part of the alter table
     */
    private function getFieldQuery(string $name, array $field):string{
        return $name.' '.$field['mtype'].
            (isset($field['length']) ? ' ('.$field['length'].')' : '').
            ($field['nullable'] ? ' NULL' : ' NOT NULL').
            (isset($field['default']) ? ' default '.$field['default'] : '').
            (isset($field['extra']) ? ' '.$field['extra'] : '');
    }

    /**
     * Rollback migration to a specific version or the previous based on env_version
     * 
     * @param null|int $version=null Version to roolback to, null to get the previous
     * 
     * @return void
     */
    public function rollbackAction(null|int $version=null):void{
        $currentVersion = Migration::getCurrentVersion();
        $migrations = glob($this->config->application->migrationsDir.'*.php');
        if(!isset($version)){
            $version = count($migrations)-1;
        }
        if((int)$version >= $currentVersion){
            Cli::error('Nothing to migrate');
        }
        for($i=$currentVersion; $i>$version; $i--){
            $class= "MigrationVersion".$i; 
            $migration = new $class();
            $this->executeQueries($migration->down());           
           
            Migration::setCurrentVersion($i-1);
        }
    }

}