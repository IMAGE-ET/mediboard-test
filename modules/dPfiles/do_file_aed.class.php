<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

class CFileAddEdit extends CDoObjectAddEdit {

  function CFileAddEdit() {
    global $m, $_POST;    
    $selKey   = intval(mbGetValueFromPost("file_object_id", 0));
    $selClass = mbGetValueFromPost("file_class"    , "");
    
    $this->CDoObjectAddEdit("CFile", "file_id");
    
    $this->createMsg = "Fichier créé";
    $this->modifyMsg = "Fichier modifié";
    $this->deleteMsg = "Fichier supprimé";
    
    $this->redirect = "m=$m"; 
    
    if ($dialog = dPgetParam($_POST, "dialog")) {
      $this->redirect      .= "&a=upload_file&dialog=1";
      $this->redirectStore = "m=$m&a=upload_file&dialog=1&uploadok=1";
    }

  }

  function bindFilePart(){
    
  }
  
  function doStore() {
    global $AppUI,$_POST;
    $upload     = null;
    $multifiles = false;    
    $msgs       = array("file_ok"=>0, "file_size_error"=>0, "file_upload_error"=>0);

    if(isset($_FILES["formfile"])){
      $aFiles = array();
      $upload =& $_FILES["formfile"];
      $_file_category_id = mbGetValueFromPost("_file_category_id");
      if(is_array($upload["name"])){
        // Plusieurs fichiers
        $multifiles = true;
        foreach($upload["error"] as $fileNumber=>$etatFile){
         $upload ;
         $aFiles[] = array("name"             => $upload["name"][$fileNumber],
                            "type"             => $upload["type"][$fileNumber],
                            "tmp_name"         => $upload["tmp_name"][$fileNumber],
                            "error"            => $upload["error"][$fileNumber],
                            "size"             => $upload["size"][$fileNumber],
                            "file_category_id" => $_file_category_id[$fileNumber],
                            "file_object_id"   => $_POST["file_object_id"],
                            "file_class"       => $_POST["file_class"],);
        }
      }else{
        // 1 seul fichier
        $aFiles[] = $upload;
      }
      
      foreach($aFiles as $file){
        if($file["error"] == 0){
          if($multifiles){
            $this->_obj = new $this->_obj->_class_name;
          }
          $this->_obj->bind($file);
          $this->_obj->file_name = $file["name"];
          $this->_obj->file_type = $file["type"];
          $this->_obj->file_size = $file["size"];
          $this->_obj->file_date = db_unix2dateTime(time());
          $this->_obj->file_real_filename = uniqid(rand());
            
          $res = $this->_obj->moveTemp($file); 
          if(!$res){
            $msgs["file_upload_error"]++;
          }else{
            if (!$this->_obj->file_id) {
              $this->_obj->file_owner = $AppUI->user_id;
            }
            if ($msg = $this->_obj->store()) {
              $msgs["file_upload_error"]++;
            }else{
              $msgs["file_ok"]++;
              $this->doLog("store");
            }
          }
        }elseif($file["error"] <=3){
          $msgs["file_size_error"] ++;
        }
      }
      // Redaction du message et renvoi
      $header = array();
      if($msgs["file_ok"]){           $header[] = $msgs["file_ok"]." fichier(s) uploadé(s)";}
      if($msgs["file_upload_error"]){ $header[] = $msgs["file_upload_error"]. " fichier(s) non envoyé(s)";}
      if($msgs["file_size_error"]){   $header[] = $msgs["file_size_error"] . " fichier(s) trop volumineux";}
      $msgNo = ($msgs["file_upload_error"] + $msgs["file_size_error"]) ? UI_MSG_ERROR : UI_MSG_OK;
      $complete_msg = implode(" - ", $header);
      $AppUI->setMsg($complete_msg, $msgNo);

      if ($msgs["file_ok"] && $this->redirectStore) {
        $this->redirect =& $this->redirectStore;
      }
      if (!$msgs["file_ok"] && ($msgs["file_upload_error"] || $msgs["file_size_error"]) && $this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    }
  }  
  
  function doRedirect() {
    global $AppUI, $_POST;
    $cat_id   = intval(mbGetValueFromPost("file_category_id"));
    if ($this->ajax) {
      $idName = $this->objectKeyGetVarName;
      $idValue = $this->_obj->$idName;
      $callBack = $this->callBack;
      echo $AppUI->getMsg();
      if ($callBack) {
        echo "\n<script type='text/javascript'>$callBack($idValue);</script>";
      }
      exit;
    }     
    if ($this->redirect !== null) {
      $AppUI->redirect($this->redirect);
    }
  }
}
?>