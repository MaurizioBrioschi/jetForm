<?php
/**
 * @author Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * @version 0.1 
 * Class for form validation
 */
class ValidatorForm extends Validator{
    protected $fields = array();
    private $fields_error = array();
    private $messageError = "";
    private $isFormValid;
    
    function __construct($connection=null,$recordset=array()) {      
        parent::__construct($connection);
        foreach(array_keys($recordset) as $key){
                $this->__set($key, $recordset[$key]);
        }
    }
    /**
     * set an attribute
     * @param int $index
     * @param mixed $value 
     */
    public function __set($index, $value)
    {
	$this->fields[$index] = $value;
    }  
    /**
     * get an attribute
     * @param mixed $index
     * @return mixed 
     */
    public function __get($index)
    {
            return $this->fields[$index];
    }
    
    /**
     * sleep method for serialize object
     * @return mixed array
     */
      function __sleep()
      {
          $array = array();
          foreach(array_keys($recordset) as $key){
                array_push($array,$key);
          }
          return $array;

      }
     /**
      * wakeup method for serialize object
      * @return ManagerUser
      */
     function __wakeup()
     {
            $this->output();
     }
    /**
     * tell if form is valis
     * @return type
     */
    public function getIsFormValid()    {
        $this->checkForm();
        return $this->isFormValid;
    }
    /**
     * get array of error message
     * @return type
     */
    public function getErrorMessage()    {
        return $this->messageError;
    }
    /**
     * get not valid error
     * @return type
     */
    public function getFieldsError()    {
        return $this->fields_error;
    }
    /**
     * tell if $field is not valid
     * @param type $field
     * @return boolean
     */
    public function isFieldError($field)    {
        for($i=0;$i<count($this->fields_error);$i++)    {
            if($this->fields_error[$i]==$field) 
                return true;
        }
    }
    /**
     * check if form is valid
     */
    private function checkForm()   {
       $this->isFormValid = true;
       $this->fields_error = array();
       foreach(array_keys($this->fields) as $key){
           $functions = explode(",",$this->findCheckFunction($key));
           foreach($functions as $function) {
                    if(strlen($function)>0)  {
                    list($return_value,$message) = $this->$function($this->$key);
                    if(!$return_value){
                        $this->messageError .= str_replace("{field}",$key,$message)."<br />";
                        $this->isFormValid = false;
                        array_push($this->fields_error, $key);
                    }
                }
           }
       }
       //check field depending by another
       foreach(array_keys($this->fields) as $key){
           $functions = explode(",",$this->findCheckFunctionConditional($key));
           foreach($functions as $function) {
               if(strlen($function)>0)  {
                list($return_value,$message) = $this->$function($this->$key);
                if(!$return_value){
                    $this->messageError .= str_replace("{field}",$key,$message)."<br />";
                    $this->isFormValid = false;
                    array_push($this->fields_error, $key);
                }
            }
           }
       }
    }
    /**
     * get the function to validate $key
     * @param type $key
     * @return string
     */
    private function findCheckFunction($key)    {
        if($key=='name'||$key=='surname'||$key=='sex'||$key=='job'||$key=='phone'|| $key=='address' || $key=='city' || $key=='nation')    {
            return "isNotEmpty";
        }else if($key=='email'){
            return "isEmailValid,checkIfInDB";
        }else if($key=='age'){
            $this->$key = intval($this->$key);
            return "isIntegerInRange";
        }else if($key=='privacy1' || $key=='privacy2'){
            return "isFlaged";
        }
        return "";
    }
    /**
     * get the function to validate $key if it depends by another field
     * @param type $key
     * @return string
     */
    private function findCheckFunctionConditional($key)    {
        if($key=='email_partner' && in_array("email", $this->fields_error)===FALSE){
                if(strlen($this->$key)>0)
                    return "isEmailValid,checkIfInDB";
                else 
                    return "";
        }
        return "";
    }
    
    
}
?>
