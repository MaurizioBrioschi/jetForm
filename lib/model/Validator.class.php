<?php
/**
 * Generic class for validator
 * @author Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * @version 0.1 
 * 
 */
class Validator{
    protected $dbConnecetion;
    
   function __construct($connection=null) {
        if($connection!=null)
            $this->dbConnecetion=$connection;
    }
    /**
     * is always true
     * @param type $value
     * @return boolean
     */
    public function getTrue($value) {
        return true;
    }
    /**
     * check if field is empty
     * @param type $value
     * @return boolean
     */
    public function isNotEmpty($value){
        if($value=='' || strlen($value)<1){
            return(array(false,"{field} is mandatory"));
        }else
            return array(true,"");
    }
    /**
     * check if is a valid email
     * @param type $email
     * @return boolean
     */
    public function isEmailValid($email)    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
               return(array(false,"{field} is not valid"));
        }else
            return array(true,"");
    }
    /**
     * check if integer is in range $min  $max, with $equal a false by default
     * @param type $value
     * @param type $min
     * @param type $max
     * @param type $equal
     * @return boolean
     */
    public function isIntegerInRange($value,$min=0, $max=100,$equal=false){
        if($equal)  {
             if(intval($value)>=$min && intval($value)<=$max){
               return(array(false,"{field} not valid, the range is between $min and $max"));
            }else{
                return array(true,"");
            }
        }else{
            if(intval($value)>$min && intval($value)<$max){
               return(array(false,"{field} not valid, the range is between $min and $max"));
            }else{
                return array(true,"");
            }
        }
    }
    /**
     * check if $value is greter than zero
     * @param type $value
     * @return type
     */
    public function isFlaged($value)    {
        if(intval($value)>0)    
            return array(true,"");
        else {
            return array(false,"You must accept {field}");
        }
    }
    /**
     * check if  $field with $value is in database
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
                return array(false,"<strong>An error occured: ".$e->getMessage()."</strong><br />");
                $this->isFormValid = false;
            }
        }else{
            return array(false,"");
        }
    }
    
}
?>
