<?php
 error_reporting(E_ALL);
 /*** define the site path ***/
 $site_path = realpath(dirname(__FILE__));

 define ('__SITE_PATH', $site_path);
 
 include __SITE_PATH . '/conf/' . 'db.php';
 /*** include the template class ***/
 include __SITE_PATH . '/conf/' . 'vars.php';
 /*** auto load model classes ***/
 function __autoload($class_name) {
    $filename = $class_name . '.class.php';
    $file = __SITE_PATH . '/lib/model/' . $filename;
    if (file_exists($file) == false)
    {
        return false;
    }
     include ($file);
     return true;
}

$repository = new DBRepository(MySqlConnection::Connection($db_host,$db_user,$db_password,$database));
date_default_timezone_set($timezone);

?>
