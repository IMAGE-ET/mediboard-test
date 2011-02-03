<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

class CFileAddEdit extends CDoObjectAddEdit {
  function CFileAddEdit() {
    global $m;    
    $selKey   = intval(CValue::post("object_id", 0));
    $selClass = CValue::post("object_class"    , "");
    
    $this->CDoObjectAddEdit("CFile", "file_id");
    
    $this->redirect = "m=$m"; 
    
    if ($dialog = CValue::post("dialog")) {
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

    if (CValue::POST("_from_yoplet") == 1) {
    	$obj = $this->_obj;
    	$array_file_name = array();
    	
    	// On retire les backslashes d'escape
    	$file_name = stripslashes($this->request['_file_path']);
    	
    	// Récupération du nom de l'image en partant de la fin de la chaîne
    	// et en rencontrant le premier \ ou /
    	preg_match('@[\\\/]([^\\\/]*)$@i', $file_name, $array_file_name);
    	$file_name = $array_file_name[1];
    	
    	$extension = strrchr($file_name, '.');
    	$_rename = $this->request['_rename'] ? $this->request['_rename'] : 'upload';
    	$file_path = "tmp/". $this->request['_checksum'];
      
    	$obj->file_name = $_rename == 'upload' ? $file_name : $_rename . $extension;
      $obj->_old_file_path = $this->request['_file_path'];
    	$obj->file_size = filesize($file_path);
    	$obj->file_owner = CAppUI::$user->_id;
      $obj->fillFields();
      $obj->updateFormFields();
      $obj->file_type = CMbPath::guessMimeType($file_name);
    	if ($msg = $obj->store()) {
    	  CAppUI::setMsg($msg, UI_MSG_ERROR);
    	} else {
    		$obj->forceDir();
    	  $obj->moveFile($file_path);
    	}
    	return parent::doStore();
    }
    
    if (isset($_FILES["formfile"])) {
      $aFiles = array();
      $upload =& $_FILES["formfile"];
      $_file_category_id = CValue::post("_file_category_id");
      $for_identite = CValue::post("for_identite", 0);
      $rename = CValue::post("_rename");
      
      CValue::setSession("_rename", $rename);
      
      foreach($upload["error"] as $fileNumber => $etatFile){
        if (!$for_identite) {
          $rename = $rename ? $rename . strrchr($upload["name"][$fileNumber], '.') : "";
        }
        
        $aFiles[] = array(
          "name"             => $upload["name"][$fileNumber],
          "type"             => $upload["type"][$fileNumber],
          "tmp_name"         => $upload["tmp_name"][$fileNumber],
          "error"            => $upload["error"][$fileNumber],
          "size"             => $upload["size"][$fileNumber],
          "file_category_id" => $_file_category_id,
          "object_id"        => CValue::post("object_id"),
          "object_class"     => CValue::post("object_class"),
          "_rename"          => $rename
        );
      }

      foreach ($aFiles as $file) {
        if ($file["error"] == UPLOAD_ERR_NO_FILE) {
          continue;
        }
        
        if ($file["error"] != 0) {
          CAppUI::setMsg(CAppUI::tr("CFile-msg-upload-error-".$file["error"]), UI_MSG_ERROR);
          continue;
        }
        
        // Reinstanciate

        $this->_obj = new $this->_obj->_class_name;
        $obj = $this->_obj;
        
        $obj->bind($file);
        $obj->file_name = empty($file["_rename"]) ? $file["name"] : $file["_rename"];
        $obj->file_type = $file["type"];
        $obj->file_size = $file["size"];
        $obj->fillFields();
        $obj->private   = CValue::post("private");
        if (false == $res = $obj->moveTemp($file)) {
          CAppUI::setMsg("Fichier non envoyé", UI_MSG_ERROR);
          continue;
        }

        // File owner on creation
        if (!$obj->file_id) {
          $obj->file_owner = $AppUI->user_id;
        }

        if ($msg = $obj->store()) {
          CAppUI::setMsg("Fichier non enregistré: $msg", UI_MSG_ERROR);
          continue;
        }

        CAppUI::setMsg("Fichier enregistré", UI_MSG_OK);
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
}
?>