<?php
/**
 * @author Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * @version 0.1 
 * Classe per la gestione della connessione al database mysql
 */
 class MySqlConnection{
        
        protected $link;
        private $server, $username, $password, $db;
        protected static $myConnection;
        
        /**
         * 
         * @param string $server
         * @param string $username
         * @param string $password
         * @param string $db 
         */
        protected function __construct($server, $username, $password, $db)
        {
            $this->server = $server;
            $this->username = $username;
            $this->password =$password;
            $this->db = $db;
            $this->connect();
        }
        /**
         * istanzia una connessione ad un db mysql, attraverso il pattern singleton garantisce che ve ne sia una sola
         * @param string $server
         * @param string $username
         * @param string $password
         * @param stribng $db
         * @return MySqlConnection 
         */
        public static function Connection($server, $username, $password, $db)
        {
            if (!isset(self::$myConnection)) {
                $c = __CLASS__;
                self::$myConnection = new $c($server, $username, $password, $db);
            }

            return self::$myConnection;
        }
        /**
         * connette ad un db mysql
         */
        private function connect()
        {
            $this->link = mysql_connect($this->server, $this->username, $this->password,true) or die("Impossible to connect to mySql DB: ". mysql_error());;
            mysql_select_db($this->db, $this->link) or die("Schema don't exist: ".mysql_error());	
        }
        
        /**
         * sleep method for serialize object
         * @return serialize array
         */
        public function __sleep()
        {
            return array('server', 'username', 'password', 'db');
        }
        /**
         * wakeup method for serialize object
         * @return the connection
         */
        public function __wakeup()
        {
            
            $this->connect();
        }
        /**
         * non permette la clonazione della connessione
         */
        public function __clone()
        {
            echo 'connection already exist!';
            trigger_error('Clone is not allowed.', E_USER_ERROR);
        }
        
        
        /*
         * Esegue una query sul database
         * @param string $statement
         */
        function exeSQL ($statement) {
           
            $result= mysql_query($statement,$this->link);
            if($result > 0){
                $num_rows = @mysql_numrows($result); 
                return array($result, $num_rows);
            }else
                throw new Exception(mysql_error());


        }
        /*
         * Inizia una transazione
         */
        function beginTransaction() { 
   
          @mysql_query("BEGIN");
        }
        /*
         * Esegue una commit
         */
        function CommitTransaction()  {
            @mysql_query("COMMIT");
        }
        /*
         * Esegue un rollback
         */
        function RollBackTransaction()  {
            @mysql_query("ROLLBACK");
        }
        /*
         * Ritorna una riga di un record set
         * @param array $result
         * @param int $i
         */
        function getResult ($result,$i=-1) {
            if ($i >= 0) {
                @mysql_data_seek($result,$i);
            }
            return mysql_fetch_array($result);
        }
        
        /*
         * Ritorna il numero field di un recordset
         * @param array $result
         */
        public function getNumFields ($result) 
        {
          return @mysql_num_fields($result); 
        }
        /**
         * pulisce il campo per la query a db
         * @param type $field
         * @return type
         */
        public function cleanField($field)	{
            $field = addslashes($field);
            if (!get_magic_quotes_gpc()) {
                $field = stripslashes($field);
            }
            
            $field = mysql_real_escape_string($field);
            return $field;
        }
        /**
         * Log Error segnala un errore nella tabella degli errori su db, dove è presente una tabella con questi campi
         * @param string $msg
         */
        public function LogError($msg)  {
            $e->error_get_last();
            return $msg."->".$e["message"];
        }
        
        
}//SqlConnectionClass

?>

