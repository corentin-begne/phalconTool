<?
use Phalcon\Tools\Cli,
Phalcon\Builder\Migration,
Phalcon\Text as Utils;
class MigrationTask extends \Phalcon\CLI\Task
{
    public function mainAction() {

    }

    public function runAction(){
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

    private function executeQueries($data){
        foreach($data['tables'] as $action => $tables){
            foreach($tables as $table){
                try{
                    $query = $action.' TABLE '.$table;
                    if($action === 'create'){
                        $query .= ' (';
                        foreach($data['fields']['add'][$table] as $name => $field){
                            $query .= $this->getFieldQuery($name, $field).',';  
                            if(isset($field['extra']) && $field['extra'] === 'auto_increment'){
                                $query .= 'PRIMARY KEY ('.$name.'),';
                            }                          
                        }
                        unset($data['fields']['add'][$table]);
                        $query = trim($query, ',').')';
                    }
                    $this->db->execute($query);
                    Cli::success($query, true);
                } catch(PDOException $e){
                    Cli::error($e->getMessage());
                }
            }
        }

        foreach($data['fields'] as $action => &$tables){
            foreach($tables as $table => &$fields){
                foreach($fields as $name => &$field){
                    try{
                        $query = $this->createAlter($table, $action, $name, $field);
                        $this->db->execute($query);
                        Cli::success($query, true);
                    } catch(PDOException $e){
                        Cli::error($e->getMessage());
                    }
                }
            }
        }
    }

    private function createAlter($table, $action, $name, $field){
        if($action === 'drop'){
            return 'ALTER TABLE '.$table.' DROP COLUMN '.$name;
        } else {
            return 'ALTER TABLE '.$table.' '.$action.' '.$this->getFieldQuery($name, $field);
        }
    }

    private function getFieldQuery($name, $field){
        return $name.' '.$field['type'].
            (isset($field['length']) ? ' ('.$field['length'].')' : '').
            ($field['isNull'] ? ' NULL' : ' NOT NULL').
            (isset($field['default']) ? ' default '.$field['default'] : '').
            (isset($field['extra']) ? ' '.$field['extra'] : '');
    }

    public function rollbackAction($params=[]){
        $currentVersion = Migration::getCurrentVersion();
        $migrations = glob($this->config->application->migrationsDir.'*.php');
        if(count($params)===0){
            $version = count($migrations)-1;
        } else {
            $version = (int)$params[0];
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