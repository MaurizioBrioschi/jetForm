<?php
/**
 * @author Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * @version 0.1 
 * Classe generica di validatori
 */
class Validator{
    protected $dbConnecetion;
    
   function __construct($connection=null) {
        if($connection!=null)
            $this->dbConnecetion=$connection;
    }
    /**
     * ritorna sempre true
     * @param type $value
     * @return boolean
     */
    public function getTrue($value) {
        return true;
    }
    /**
     * verifica se il campo è vuoto 
     * @param type $value
     * @return boolean
     */
    public function isNotEmpty($value){
        if($value=='' || strlen($value)<1){
            return(array(false,"Devi indicare {field}"));
        }else
            return array(true,"");
    }
    /**
     * verifica se è un email valida
     * @param type $email
     * @return boolean
     */
    public function isEmailValid($email)    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
               return(array(false,"Devi indicare un {field} valida"));
        }else
            return array(true,"");
    }
    /**
     * varifica se l'intero è nel range $min e $max, con $equal a false di default
     * @param type $value
     * @param type $min
     * @param type $max
     * @param type $equal
     * @return boolean
     */
    public function isIntegerInRange($value,$min=0, $max=100,$equal=false){
        if($equal)  {
             if(intval($value)>=$min && intval($value)<=$max){
               return(array(false,"{field} non valido, deve essere compreso o uguale a $min e $max"));
            }else{
                return array(true,"");
            }
        }else{
            if(intval($value)>$min && intval($value)<$max){
               return(array(false,"{field} non valido, deve essere compreso tra $min e $max"));
            }else{
                return array(true,"");
            }
        }
    }
    /**
     * Verifica se $value è maggiore di zero
     * @param type $value
     * @return type
     */
    public function isFlaged($value)    {
        if(intval($value)>0)    
            return array(true,"");
        else {
            return array(false,"Devi accettare {field}");
        }
    }
    /**
     * Controlla se il $field con $value è presente a database
     * @param type $value
     * @param type $field
     * @return type
     */
    public function checkIfInDB($value,$field="email") {
        if(strlen($value)>0)    {
        $SQL = "SELECT * FROM users WHERE $field='$value'";
            try {
                list($dbd,$i) = $this->dbConnecetion->exeSQL($SQL);
                if($i>0){
                    return array(false,"{field} già presente a database");
                    
                }else{
                    return array(true,"");
                }
            }catch(Exception $e)    {
                return array(false,"<strong>Si è verificato un errore di sistema: ".$e->getMessage()."</strong><br />");
                $this->isFormValid = false;
            }
        }else{
            return array(false,"");
        }
    }
    
}
?>
