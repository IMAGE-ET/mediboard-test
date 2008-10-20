<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

class CFileAddEdit extends CDoObjectAddEdit {
  function CFileAddEdit() {
    global $m;    
    $selKey   = intval(mbGetValueFromPost("object_id", 0));
    $selClass = mbGetValueFromPost("object_class"    , "");
    
    $this->CDoObjectAddEdit("CFile", "file_id");
    
    $this->redirect = "m=$m"; 
    
    if ($dialog = dPgetParam($_POST, "dialog")) {
      $this->redirect      .= "&a=upload_file&dialog=1";
      $this->redirectStore = "m=$m&a=upload_file&dialog=1&uploadok=1";
    }
  }

  function bindFilePart(){
    
  }
  
  function doStore() {
    global $AppUI;
    $upload     = null;
    $multifiles = false;

    if (isset($_FILES["formfile"])) {
      $aFiles = array();
      $upload =& $_FILES["formfile"];
      $_file_category_id = mbGetValueFromPost("_file_category_id");
      if (is_array($upload["name"])) {
        // Plusieurs fichiers
        $multifiles = true;
        foreach($upload["error"] as $fileNumber=>$etatFile){
         $upload ;
         $aFiles[] = array("name"              => $upload["name"][$fileNumber],
                            "type"             => $upload["type"][$fileNumber],
                            "tmp_name"         => $upload["tmp_name"][$fileNumber],
                            "error"            => $upload["error"][$fileNumber],
                            "size"             => $upload["size"][$fileNumber],
                            "file_category_id" => $_file_category_id[$fileNumber],
                            "object_id"   => $_POST["object_id"],
                            "object_class"       => $_POST["object_class"],);
        }
      }
      else{
        // 1 seul fichier
        $aFiles[] = $upload;
      }
      
      foreach ($aFiles as $file) {
        if ($file["error"] == UPLOAD_ERR_NO_FILE) {
          continue;
        }
        
        if ($file["error"] != 0) {
          $AppUI->setMsg(CAppUI::tr("CFile-msg-upload-error-". $file["error"]), UI_MSG_ERROR);
          continue;
        }
        
        // Reinstanciate
        if ($multifiles){
          $this->_obj = new $this->_obj->_class_name;
        }
        
        $this->_obj->bind($file);
        $this->_obj->file_name = $file["name"];
        $this->_obj->file_type = $file["type"];
        $this->_obj->file_size = $file["size"];
        $this->_obj->file_date = mbDateTime();
        $this->_obj->file_real_filename = uniqid(rand());
            
        if (false == $res = $this->_obj->moveTemp($file)) {
          $AppUI->setMsg("Fichier non envoyé", UI_MSG_ERROR);
          continue;
        } 

        // File owner on creation
        if (!$this->_obj->file_id) {
          $this->_obj->file_owner = $AppUI->user_id;
        }

        if ($msg = $this->_obj->store()) {
          $AppUI->setMsg("Fichier non enregistré", UI_MSG_ERROR);
          continue;
        }

        $AppUI->setMsg("Fichier enregistré", UI_MSG_OK);
      }
      
      // Redaction du message et renvoi
      if (@count($AppUI->messages[UI_MSG_OK]) && $this->redirectStore) {
        $this->redirect =& $this->redirectStore;
      }
      
      if (!@count($AppUI->messages[UI_MSG_OK]) && $this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    }
    else {
      parent::doStore();
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
      CApp::rip();
    }     
    if ($this->redirect !== null) {
      $AppUI->redirect($this->redirect);
    }
  }
}
?>