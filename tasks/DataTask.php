<?
use Phalcon\Tools\Cli,
\Phalcon\CLI\Task;

/**
 * Task managing database data
 */
class DataTask extends Task
{
    /**
     * Main task action (not implemented)
     */
    public function mainAction():void{

    }

    /**
     * Import files translations data into database
     * 
     * @return void
     */
    public function importAction():void { 
        if($this->config->application->dumpDir === ''){
            Cli::error('dumpDir path is empty');
        }
        exec('rm -rf '.$this->config->application->dumpDir.'*.log');       
        foreach(glob($this->config->application->dumpDir.'*.csv') as $file){
            $table = basename($file, '.csv');
            $log = $this->config->application->dumpDir.$table.'.log';
            echo $table."\n";
            if(defined('TRUNCATE')){
                // truncate
                echo '  Truncate ';
                $query = 'SET FOREIGN_KEY_CHECKS = 0;DELETE FROM '.$table.';SET FOREIGN_KEY_CHECKS = 1;';
                exec('export MYSQL_PWD='.escapeshellarg($this->config[ENV]->database->password).';mysql -u '.$this->config[ENV]->database->username.' --database '.$this->config[ENV]->database->dbname.' -h '.$this->config[ENV]->database->host.' --ssl-mode=DISABLED --port 3306 -ss -e "'.$query.'"  > /dev/null 2>>'.$log);
                $this->checkLog($log);
                Cli::success('Success', true);
            }
            // insert/replace
            if(defined('Truncate')){
                echo '  Insert ';
            } else {
                echo '  Replace ';
            }
            $query = 'SET FOREIGN_KEY_CHECKS = 0;LOAD DATA LOCAL INFILE \''.$file.'\' REPLACE INTO TABLE '.$table.' FIELDS TERMINATED BY \'\t\' LINES TERMINATED BY \'\n\';SET FOREIGN_KEY_CHECKS = 1;';
            exec('export MYSQL_PWD='.escapeshellarg($this->config[ENV]->database->password).';mysql --local-infile --show-warnings --verbose --default-character-set=utf8 -u '.$this->config[ENV]->database->username.' --database '.$this->config[ENV]->database->dbname.' -h '.$this->config[ENV]->database->host.' --ssl-mode=DISABLED --port 3306 -e "'.$query.'" >> /dev/null 2>>'.$log);
            $this->checkLog($log);
            Cli::success('Success', true);
        }
    }

    /**
     * Check and remove log about command return
     * 
     * @param string $log Path to the log file
     * 
     * @return void
     */
    private function checkLog(string $log):void{
        if(file_exists($log)){
            if(file_get_contents($log) === ''){
                exec('rm -f '.$log);
            } else {
                Cli::error('Failed'."\n".file_get_contents($log));
            }
        }
    }

    /**
     * Export translations from database to files
     * 
     * @param null|string $tables=null Tables List separated by a comma or null to export all
     * 
     * @return void
     */
    public function exportAction(null|string $tables=null):void {
        $tables = isset($tables) ? explode(',', $tables) : [];
        if($this->config->application->dumpDir === ''){
            Cli::error('dumpDir path is empty');
        }
        //clean folder
        exec('rm -rf '.$this->config->application->dumpDir.'*');
        foreach($this->db->listTables($this->config[ENV]->database->dbname) as $table){
            if(count($tables) === 0 || in_array($table, $tables)){
                echo $table.' ';
                $query = 'SELECT * FROM '.$table;
                $csv = $this->config->application->dumpDir.$table.'.csv';
                $log = $this->config->application->dumpDir.$table.'.log';
                exec('export MYSQL_PWD='.escapeshellarg($this->config[ENV]->database->password).';mysql -u '.$this->config[ENV]->database->username.' --skip-column-names --default-character-set=utf8 --database '.$this->config[ENV]->database->dbname.' -h '.$this->config[ENV]->database->host.' --port 3306 -e "'.$query.'" | sed -e \'s/NULL/\\\N/g\' > '.$csv.' 2>>'.$log);
                $this->checkLog($log);
                Cli::success('Success', true);
            }
        }
    }
}