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
  }

  function doStore() {
    global $AppUI;
    $upload = null;    
    if(isset($_FILES["formfile"])) {
      $upload = $_FILES["formfile"];
      if ($upload["size"] < 1) {
        if (!$this->_obj->file_id) {
          $AppUI->setMsg("Taille de fichier nulle. Echec de l'opération.", UI_MSG_ERROR);
          $this->doRedirect($this->_obj->file_category_id);
        }
      }else{
        $this->_obj->file_name = $upload["name"];
        $this->_obj->file_type = $upload["type"];
        $this->_obj->file_size = $upload["size"];
        $this->_obj->file_date = db_unix2dateTime(time());
        $this->_obj->file_real_filename = uniqid(rand());
        
        $res = $this->_obj->moveTemp($upload);
        if(!$res){
          $AppUI->setMsg("Impossible de créer le fichier", UI_MSG_ERROR);
          if ($this->redirectError) {
            $this->redirect =& $this->redirectError;
          }
        }
      }
    }
    if (!$this->_obj->file_id) {
      $this->_obj->file_owner = $AppUI->user_id;
    }
    
    if ($msg = $this->_obj->store()) {
      $AppUI->setMsg($msg, UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    }else{
      $id = $this->objectKeyGetVarName;
      mbSetValueToSession($id, $this->_obj->$id);
      $isNotNew = @$_POST[$this->objectKeyGetVarName];
      $this->doLog("store");
      $AppUI->setMsg( $isNotNew ? $this->modifyMsg : $this->createMsg, UI_MSG_OK);
      if ($this->redirectStore) {
        $this->redirect =& $this->redirectStore;
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