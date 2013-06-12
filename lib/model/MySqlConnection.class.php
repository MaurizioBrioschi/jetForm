<?php
/**
 * @author Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * @version 0.1 
 * Class to manage MySql DB Connection
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
         * create the connection
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
         * connect to db
         */
        private function connect()
        {
            $this->link = mysql_connect($this->server, $this->username, $this->password,true) or die("Impossible to connect to mySql DB: ". mysql_error());;
            if(!mysql_select_db($this->db, $this->link)){
                try{
                     $SQL = "CREATE DATABASE  IF NOT EXISTS ".$this->db;
                     $this->exeSQL($SQL);
                     if(!mysql_select_db($this->db, $this->link)) die("Impossibile creare il database");
                }  catch (Exception $e){
                    
                }
            } 
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
         * forbit to clone db connection
         */
        public function __clone()
        {
            echo 'connection already exist!';
            trigger_error('Clone is not allowed.', E_USER_ERROR);
        }
        
        
        /*
         * exec a query to db and return an array with recordset and number of lines
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
         * begin a transaction
         */
        function beginTransaction() { 
   
          mysql_query("BEGIN");
        }
        /*
         * do commit
         */
        function CommitTransaction()  {
            @mysql_query("COMMIT");
        }
        /*
         * do rollback
         */
        function RollBackTransaction()  {
            @mysql_query("ROLLBACK");
        }
        /*
         * get the resultset for a particular recordset
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
         * return the number of fields
         * @param array $result
         */
        public function getNumFields ($result) 
        {
          return @mysql_num_fields($result); 
        }
        /**
         * clean field for db query
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
         * return the error 
         * @param string $msg
         */
        public function LogError($msg)  {
            $e->error_get_last();
            return $msg."->".$e["message"];
        }
        
        
}//SqlConnectionClass

?>

