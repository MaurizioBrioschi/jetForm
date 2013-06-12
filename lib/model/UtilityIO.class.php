<?php
/**
 * @author Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * @version 0.1 
 * class for IO managment
 */
class UtilityIO {
    /**
     * remove directory content and directory if $removeDir is true
     * @param string $path 
     * @param boolean $removeDir 
     */
    public static function removeDirectory($path,$removeDir=false)  {
        try{
             $objects = scandir($path);
             foreach ($objects as $object) {
                   if ($object != "." && $object != "..") {
                      if(is_dir($path."/".$object)){
                          UtilityIO::removeDirectory($path."/".$object);
                      }
                      else
                          UtilityIO::removeFile($path."/".$object);
                   }
             } 
             if($removeDir) {
                if(!@rmdir($path)){
                    $errors= error_get_last();
                    die($errors["message"]." at ".$errors["file"]." line ".$errors["line"]);
                }
             }
        }catch(IOException $e)  {
            die($e->getMessage());
        }
    }
    /**
     * remove file
     * @param string $filepath 
     */
    public static function removeFile($filepath)  {
        try{
             if(!@unlink($filepath))   {
                          $errors= error_get_last();
                          die($errors["message"]." at ".$errors["file"]." line ".$errors["line"]);
             }
             return true;
        }catch(IOException $e)  {
            die($e->getMessage());
            return false;
        }
    }
    /**
     * replace string in file
     * @param String $path
     * @param String $oldString
     * @param String $newString
     * @return true if it's done, false if an error is occured
     */
    public static function replaceInFile($path,$oldString,$newString){   
        try{
            $file = file_get_contents($path);
            $file = str_replace($oldString, $newString, $file);
            $handle = @fopen($path,"wb");
            file_put_contents($path, $file);
            @fclose($handle);
            
        }catch(IOException $e)    {
            die($e->getMessage());
        }
    }
    /**
     * unzip $path file in $dest folder
     * @param type $path
     * @param type $dest
     * @return type 
     */
    public static function unzipFile($path, $dest)  {
     $zip = new ZipArchive;
     $res = $zip->open($path);
     
     if ($res === TRUE) {
         
         $zip->extractTo($dest);
         $zip->close();
         $objects = scandir($dest);
         foreach ($objects as $object) {
               if ($object != "." && $object != "..") {
                  if(is_dir($dest."/".$object)){
                      rename($dest.$object, str_replace(" ", "_", $dest.$object));
                      $name = str_replace(" ", "_", $object);
                      return $name."/";
                      
                  }
                  
               }
         } 
         
     } else {
         return "";
     }
    }
    /**
     * upload file in $path with  $file_dest name, posted thrown $post_var 
     * @param string $file
     * @param string $path
     * @param string $file_dest
     * @return string $fileName o Boolen FALSE
     */
    public static function uploadFile($file,$path="",$file_dest=null)   {
        if($file_dest!=null)
            $fileName = str_replace (" ", "_", $file_dest);
        else
            $fileName = str_replace (" ", "_",$file);


        if(!get_magic_quotes_gpc())
        {
            $fileName =  addslashes ($fileName);
        }

        try{
           if(move_uploaded_file($file,$path.$fileName)){
                    return $fileName;
           }  else {
               return FALSE;
           }
        }catch(Exception $e){
            return FALSE;
        }
             
             
    }  
    /**
     * set permission to directory
     * @param string $path
     * @param int $mode
     */
    public static function chmod($path,$mode)   {
        exec("chmod -Rf $mode $path");
    }
    
    
}
?>

