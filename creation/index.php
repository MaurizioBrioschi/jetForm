<?php
/**
 * Script per la generazione automatica del form di iscrizione alla newsletter di contactlab
 */
    include('../include.php');
    function writeLog($filelog,$msg){
         $now = new DateTime();
         file_put_contents($filelog, "[ ".$now->format('d-m-Y H:m:s')." ] ".$msg."\n",FILE_APPEND);
         echo "[ ".$now->format('d-m-Y H:m:s')." ] ".$msg."<br />";
    }
    $now = new DateTime();
    $filelog = "../logs/".$logName."_creation".$now->format('YmdHms').".log";
    file_put_contents($filelog, "[ ".$now->format('d-m-Y H:m:s')." ] ------ Creazione form iscrizione --------\n");
    
    //inizio la creazione
    
    writeLog($filelog,"------ Creazione form iscrizione --------");
    writeLog($filelog,"------ Creazione Database --------");
    
    $fields = explode(",", $db_fields);
    $msg = $repository->createDB($database,$fields);
    
    writeLog($filelog,$msg);
    if(strpos($msg, "ERROR")>0)
            die();
    
    UtilityIO::removeDirectory("../form");
    writeLog($filelog,"---------- creazione index.php ---");
    try{
        $handle = fopen("../form/index.php","x+");
        if($handle===FALSE)  {
                 $error = error_get_last();
                 writeLog($filelog,"[ WARNING ] ".$error["message"]);
                 
        }
    }  catch (Exception $e) {
         writeLog($filelog,"[ WARNING ] ".$e->getMessage());
    }
    ob_start();
    include("form.php");
    $content = ob_get_contents();
    ob_end_clean();
    file_put_contents("../form/index.php", $content,FILE_APPEND);
    fclose($handle);
    writeLog($filelog,"---------- fine creazione index.html");
    writeLog($filelog,"---------- creazione pagina di conferma ---");
    try{
        $handle = fopen("../form/confirm.html","x+");
        if($handle===FALSE)  {
                 $error = error_get_last();
                 writeLog($filelog,"[ WARNING ] ".$error["message"]);
                 
        }else {
            fclose($handle);
        }
    }  catch (Exception $e) {
         writeLog($filelog,"[ WARNING ] ".$e->getMessage());
    }
    
    writeLog($filelog,"---------- fine creazione pagina di conferma");
    writeLog($filelog,"----------creazione pagina di iscrizione ---");
    try{
        $handle = fopen("../form/iscrizione.php","x+");
        if($handle===FALSE)  {
                 $error = error_get_last();
                 writeLog($filelog,"[ WARNING ] ".$error["message"]);
                 
        }else {
            fclose($handle);
        }
    }  catch (Exception $e) {
         writeLog($filelog,"[ WARNING ] ".$e->getMessage());
    }
    writeLog($filelog,"----------fine creazione pagina di iscrizione ---");
    $content = "<?php\n 
                include('../include.php');\n
                \$user = array(); //arry che identifica lo user\n";
    writeLog($filelog,"----------INSERISCO I CONTENUTI NELLA PAGINA DI ISCRIZIONE ---");
    file_put_contents("../form/iscrizione.php", $content,FILE_APPEND);
    writeLog($filelog,"----------INSERISCO POST DEL FORM ---");
    $content = "";
    foreach ($fields as $field) {
        $content .= "\$user[\"$field\"] = isset(\$_POST[\"$field\"]) ? \$_POST[\"$field\"]: '';\n";
    }
    file_put_contents("../form/iscrizione.php", $content,FILE_APPEND);
    writeLog($filelog,"----------FINE INSERIMENTO POST DEL FORM ---");
    writeLog($filelog,"---------- INSERIMENTO CODICE FORM ---");
    $fields_mandatory = explode(",", $mandatory_fields);
    
    $content ="\$validator = new ValidatorForm(\$repository->getConnection(),array(";
    foreach($fields_mandatory as $field)    {
        $content .= "\"$field\"=>\$user[\"$field\"],";
    }
    $content = substr($content, 0,  strlen($content)-1);
    $content .= "));\n
    \$errore = \"\";\n
    if(\$validator->getIsFormValid())    {\n
            //inserisco lo user a database\n
            \$user_id= \$repository->genericInsert(\"users\",\$user);\n";
    if($activeTriggered)    { 
        $content .= "if(intval(\$user_id)>0){\n
                /**\n
                 * inserisco lo user in clab\n
                 */\n
                openlog(\"\$logName\", LOG_PID | LOG_PERROR, LOG_LOCAL0);\n
                syslog(LOG_INFO, \"INFO: SCRIPT _START at \" . date(\"Y-m-d H:i:s\"));\n
                \$clabService = new ClabService();\n               
                \$clabApiClient = new ClabApiClient(\$userKey, \$apiKey, \$clabService);\n
                if(\$clabApiClient->startSession())  {\n
                        try{\n                                                              
                            \$user_clab = \$clabApiClient->addNewRecipient(\$userdb_id,\$user);\n
                            //invio la triggered\n
                            \$request_id = \$clabApiClient->triggerDeliveryByAlias(\$delivery_alias, \$user_clab->identifier);\n
                            //aggiorno il db in locale\n
                            if(\$request_id>0){      \n                      
                                \$updateUser = array();\n
                                \$updateUser[\"clab_id\"] = \$user_clab->identifier;\n
                                \$updateUser[\"inserted_at\"] = new DateTime();\n
                                \$updateUser[\"sent\"] = 1;\n
                                \$repository->genericUpdate(\"users\",\$updateUser,\$user_id);\n
                            }\n
                         }catch(Exception \$e)   {\n
                            \$errore=\"Modifica indirizzo email fallita, usa un altro indirizzo email\";\n
                         }          \n          
                   \$clabApiClient->endSession();\n
                   
                }else{\n
                    \$errore = \"Errore di sistema, riprovare più tardi\";\n
                }\n
            }\n";
    }else{
        $content .= "header('Location: confirm.html');\n
                   exit;   \n";
    }
          
    $content .= "}else{\n
        \$errore = \$validator->getErrorMessage();\n
    }\n
    ?>\n";
    file_put_contents("../form/iscrizione.php", $content,FILE_APPEND);
    
    writeLog($filelog,"---------INSERIMENTO PARTE DI GESTIONE ERRORI---");
    ob_start();
    include("form.php");
    $content = ob_get_contents();
    ob_end_clean();
    file_put_contents("../form/iscrizione.php", $content,FILE_APPEND);
    writeLog($filelog,"---------IMPOSTO I PERMESSI DI SCRITTURA AI FILE---");
    UtilityIO::chmod("../form", "777");
     writeLog($filelog,"---------CREO IL DUMP DEL DB ---");
     $now = new DateTime();
    $repository->dumpDb($db_user,$db_password,$database,"../data/dump$database".$now->format('YmdHms').".sql");
    UtilityIO::chmod("../data", "777");
    writeLog($filelog,"---------FORM DI BASE CREATO [ SE SEI QUI E NON E' UN BEL POSTO, VUOL DIRE CHE CMQ TUTTO HA FUNZIONATO! ENJOY IT E PROVA <a href=\"../form/index.php\">QUI</a> ]---");
?>
