<?php
/**
 * Script to generate form
 * 
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
    UtilityIO::removeDirectory("../form");
    writeLog($filelog,"------ FORM CREATION --------");
    writeLog($filelog,"------ DATABASE CREATION --------");
    
    $fields = explode(",", $db_fields);
    $msg = $repository->createDB($database,$fields);
    
    writeLog($filelog,$msg);
    if(strpos($msg, "ERROR")>0)
            die();
    
    
    writeLog($filelog,"---------- CREATION index.php ---");
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
    UtilityIO::chmod("../form/index.php", "777");
    writeLog($filelog,"----------index.html CREATED");
    writeLog($filelog,"----------CREATION confirm.html ---");
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
    UtilityIO::chmod("../form/confirm.html", "777");
    writeLog($filelog,"---------- confirm.html CREATED");
    writeLog($filelog,"---------- CREATION subscribe.php ---");
    try{
        $handle = fopen("../form/subscribe.php","x+");
        if($handle===FALSE)  {
                 $error = error_get_last();
                 writeLog($filelog,"[ WARNING ] ".$error["message"]);
                 
        }else {
            fclose($handle);
        }
    }  catch (Exception $e) {
         writeLog($filelog,"[ WARNING ] ".$e->getMessage());
    }
    writeLog($filelog,"----------subscribe.php CREATED ---");
    $content = "<?php\n 
                include('../include.php');\n
                \$user = array(); //arry che identifica lo user\n";
    writeLog($filelog,"---------- PUT CONTENT IN subscribe.php ---");
    file_put_contents("../form/subscribe.php", $content,FILE_APPEND);
    writeLog($filelog,"---------- INSERT FORM ---");
    $content = "";
    foreach ($fields as $field) {
        $content .= "\$user[\"$field\"] = isset(\$_POST[\"$field\"]) ? \$_POST[\"$field\"]: '';\n";
    }
    file_put_contents("../form/subscribe.php", $content,FILE_APPEND);
    writeLog($filelog,"---------- END FORM ---");
    writeLog($filelog,"---------- INSERT FORM CODE ---");
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
    
    $content .= "header('Location: confirm.html');\n
                   exit;   \n";
    
          
    $content .= "}else{\n
        \$errore = \$validator->getErrorMessage();\n
    }\n
    ?>\n";
    file_put_contents("../form/subscribe.php", $content,FILE_APPEND);
    UtilityIO::chmod("../form/subscribe.php", "777");
    writeLog($filelog,"---------INSERT ERROR PART MANAGMENT ---");
    ob_start();
    include("form.php");
    $content = ob_get_contents();
    ob_end_clean();
    file_put_contents("../form/subscribe.php", $content,FILE_APPEND);
   
     writeLog($filelog,"--------DB DUMP ---");
     $now = new DateTime();
     if(file_exists("../data")) UtilityIO::removeDirectory("../data");
     else        mkdir ("../data");
     $dumpfile = "dump".$database.$now->format('YmdHms').".sql";
     $repository->dumpDb($db_user,$db_password,$database,"../data/dump$database".$now->format('YmdHms').".sql");
    
     writeLog($filelog,"--------- ENJOY IT!!! <br />TEST IT <a href=\"../form/index.php\">HERE</a><br />DB DUMP <a href=\"../data/$dumpfile\">HERE</a> ]---");
?>
