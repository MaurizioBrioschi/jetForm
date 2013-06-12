<?php
/**
 * @author Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * @version 0.1 
 * Repository class
 */
class DBRepository  {
    private $connection;
    /**
     * Constructor
     * @param MySqlConnection $DBConnection
     */
    public function __construct(MySqlConnection $DBConnection){
        $this->connection = $DBConnection;
    }
    /**
     * get mysql db connection
     * @return MySqlConnection
     */
    public function getConnection(){
        return $this->connection;
    }
    
    /**
     * do an insert $table with $array parameters
     * @param string $table
     * @param string $array
     * @return int
     */
    public function genericInsert($table,$array){
        $SQL = "INSERT INTO $table(";
        foreach(array_keys($array) as $key){
                $SQL .= $key.",";
        }
        $SQL = substr($SQL, 0,  strlen($SQL)-1);
        $SQL .= ") VALUES (";
        foreach(array_keys($array) as $key){
            if (gettype($array[$key]) == 'object') {
                if (get_class($array[$key])=='DateTime') {
                    $SQL .= "'".  $this->connection->cleanField($array[$key]->format('Y-m-d H:i:s'))."',";
                }
            }else{
                $SQL .= "'".  $this->connection->cleanField($array[$key])."',";
            }
        }
        $SQL = substr($SQL, 0,  strlen($SQL)-1);
        $SQL .= ");";
        
        try{
            $this->connection->exeSQL($SQL);
            $SQL = "select max(id) as id FROM $table;";
            list($dbd, $i) = $this->connection->exeSQL($SQL);
            $recordset = $this->connection->getResult($dbd,0);
            return intval($recordset["id"]);
        }catch(Exception $e)    {
            return $this->connection->LogError("DBRepository->genericInsert($table)->$SQL");
            
        }
    }
    /**
     * do an update to $table cwith $array parameters on $keyField with $keyValue
     * @param string $table
     * @param type $array
     * @param string $keyValue
     * @param string $keyField
     * @return boolean
     */
    public function genericUpdate($table,$array,$keyValue,$keyField="id"){
        $SQL = "UPDATE $table SET ";
        foreach(array_keys($array) as $key){
                $SQL .= $key."='";
                if (gettype($array[$key]) == 'object') {
                    if (get_class($array[$key])=='DateTime') {
                        $SQL .=  $this->connection->cleanField($array[$key]->format('Y-m-d H:i:s'))."',";
                    }
                }else{
                    $SQL .= $this->connection->cleanField($array[$key])."',";
                }
        }
        $SQL = substr($SQL, 0,  strlen($SQL)-1);
        $SQL .= " WHERE $keyField='".$this->connection->cleanField($keyValue)."'";
        try{
            $this->connection->exeSQL($SQL);
            return true;
        }catch(Exception $e)    {
            return $this->connection->LogError("DBRepository->genericInsert($table)->$SQL");
            
        }
    }
    /**
     * get subscribers
     * @param string $where
     * @return type
     */
    public function getSubscribers($where="WHERE sent<1;")    {
        $SQL = "SELECT * FROM users $where";
        try{
            list($dbd,$i) = $this->connection->exeSQL($SQL);
            return array($dbd, $i);
        }catch(Exception $e)    {
            return;
        }
    }
    /**
     * create db
     * @param string $db
     * @param string $fields
     * @return string
     */
    public function createDB($db,$fields)   {
        try{
            $this->connection->beginTransaction();
            $SQL = "CREATE DATABASE  IF NOT EXISTS $db;";
            $this->connection->exeSQL($SQL);
            $SQL = "USE $db;";
            $this->connection->exeSQL($SQL);
            $SQL = "DROP TABLE IF EXISTS users; ";
            $this->connection->exeSQL($SQL);
            $SQL = "CREATE TABLE users (id int(11) NOT NULL AUTO_INCREMENT";
            foreach($fields as $field)  {
                $SQL .= ", $field ".$this->getFieldType($field);
            }
                $SQL .= ", PRIMARY KEY (id)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

                 $this->connection->exeSQL($SQL);
                 $this->connection->CommitTransaction();
                 return $SQL;
         }catch(Exception $e)   {
             $this->connection->RollBackTransaction();
             return "[ ERROR ]:".$e->getMessage()."<br /> [ $SQL ]";
         }
    }
    /**
     * get $field type to set a database
     * @param string $field
     * @return string
     */
    private function getFieldType($field){
        if($field=='name' || $field == 'surname'|| $field == 'email'|| $field == 'job' )  {
            return "varchar(255) DEFAULT NULL";
        }else if($field == 'sex') {
            return "char(1) DEFAULT NULL";
        }else if($field=='age'){
            return "INT(3) DEFAULT NULL'0'";
        }else if($field=='phone'){
            return "varchar(20) DEFAULT NULL";
        }else if($field=='privacy1'||$field=='privacy2')    {
            return "tinyint(1) DEFAULT NULL";
        }
        
        return "varchar(255) DEFAULT NULL";
    }
    /**
     * make mysql db dump
     * @param type $user
     * @param type $pwd
     * @param type $db
     * @param type $pathTo
     */
    public function dumpDb($user,$pwd,$db,$pathTo)   {
        try{
            exec("mysqldump -u$user -p$pwd $db > $pathTo");
        }catch(Exception $e){
            die($e->getMessage());
        }
    }
}
?>
