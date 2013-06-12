<?php
/**
 * @author Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * @version 0.1 
 * Classe generica per la validazione del form
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
     * Setta un attributo della pagina
     * @param int $index
     * @param mixed $value 
     */
    public function __set($index, $value)
    {
	$this->fields[$index] = $value;
    }  
    /**
     * Ottiene il valore di un attributo della pagina
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
     * Indica se il form è valido
     * @return type
     */
    public function getIsFormValid()    {
        $this->checkForm();
        return $this->isFormValid;
    }
    /**
     * Ritorna l'array degli errori del form
     * @return type
     */
    public function getErrorMessage()    {
        return $this->messageError;
    }
    /**
     * Ritorna l'array contenente i campi non validi del form
     * @return type
     */
    public function getFieldsError()    {
        return $this->fields_error;
    }
    /**
     * Indica se il $field è un campo non valido
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
     * Controlla se il form è valido
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
     * Ritorna la funzione di validazione del campo $key
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
     * Ritorna la funzione di validazione del campo $key se questa dipende dalla validazione di un altra campo
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
