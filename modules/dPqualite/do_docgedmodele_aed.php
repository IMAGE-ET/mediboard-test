<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $canAdmin, $m, $g;

$doc_ged_id = 0;
$file_id = null;
$_firstModeleGed  = mbGetValueFromPost("_firstModeleGed", null);
$erreur_file=null;

class CDoDocGedAddEdit extends CDoObjectAddEdit {
  function CDoDocGedAddEdit() {
    global $AppUI;
    $this->CDoObjectAddEdit("CDocGed", "doc_ged_id");
    
    $this->createMsg = $AppUI->_("msg-".$this->className."-create_modele");
    $this->modifyMsg = $AppUI->_("msg-".$this->className."-modify_modele");
    $this->deleteMsg = $AppUI->_("msg-".$this->className."-delete_modele");
  }
  
  function doBind() {
    global $AppUI;
    
    $this->ajax = mbGetValueFromPost("ajax");
    $this->suppressHeaders = mbGetValueFromPost("suppressHeaders");
    $this->callBack = mbGetValueFromPost("callback");
    unset($_POST["ajax"]);
    unset($_POST["suppressHeaders"]);
    unset($_POST["callback"]);
    
    // Object binding
    if (!$this->_obj->bind( $_POST["ged"] )) {
      $AppUI->setMsg( $this->_obj->getError(), UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
      $this->doRedirect();
    }
    $this->_objBefore->load($this->_obj->_id);
  }


  function doStore() {
    global $AppUI, $doc_ged_id, $file_id, $_firstModeleGed;
    
    $file_upload_ok = false;
    
    if ($msg = $this->_obj->store()) {
      $AppUI->setMsg($msg, UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
      $objFile->delete();
    }else{
      $this->redirect = null;
      $doc_ged_id = $this->_obj->doc_ged_id;
      if(isset($_FILES["formfile"]) && $_FILES["formfile"]["name"]!="") {
        $objFile = new CFileAddEdit;
        $objFile->redirect = null;
        $objFile->doBind();
        $objFile->_obj->file_object_id = $doc_ged_id;
        $objFile->dostore();
        if($AppUI->msgNo == UI_MSG_OK){
          $file_upload_ok = true;
          $file_id = $objFile->_obj->file_id;
        }else{
          // Erreur Upload
          if ($this->redirectError) {
            $this->redirect =& $this->redirectError;
          }
          $this->doRedirect();
        }
      }
    } 
  }
}


class CDoDocGedSuiviAddEdit extends CDoObjectAddEdit {
  function CDoDocGedSuiviAddEdit() {
    global $AppUI;
    $this->CDoObjectAddEdit("CDocGedSuivi", "doc_ged_suivi_id");
    
    $this->createMsg = $AppUI->_("msg-".$this->className."-create_modele");
    $this->modifyMsg = $AppUI->_("msg-".$this->className."-modify_modele");
    $this->deleteMsg = $AppUI->_("msg-".$this->className."-delete_modele");
  }
  
  function doBind() {
    global $AppUI;
    
    $this->ajax = mbGetValueFromPost("ajax");
    $this->suppressHeaders = mbGetValueFromPost("suppressHeaders");
    $this->callBack = mbGetValueFromPost("callback");
    unset($_POST["ajax"]);
    unset($_POST["suppressHeaders"]);
    unset($_POST["callback"]);
    
    // Object binding
    if (!$this->_obj->bind( $_POST["suivi"] )) {
      $AppUI->setMsg( $this->_obj->getError(), UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
      $this->doRedirect();
    }
    $this->_objBefore->load($this->_obj->_id);
  }
  
  function doStore() {
    global $AppUI,$doc_ged_id,$file_id,$_validation;
    $this->_obj->date       = mbDateTime();
    $this->_obj->remarques  = $AppUI->_("Modele");
    $this->_obj->doc_ged_id = $doc_ged_id;
    if($file_id !== null){
      $this->_obj->file_id  = $file_id;
      $this->_obj->doc_ged_suivi_id = null;
    }
    if ($msg = $this->_obj->store()) {
      $AppUI->setMsg($msg, UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    }    
  }  
} 


$do1 = new CDoDocGedAddEdit;
if(!$canAdmin){
  $this->doRedirect();
}
$do1->doIt();

if($file_id){
  $do2 = new CDoDocGedSuiviAddEdit;
  $do2->doIt();
}elseif($_firstModeleGed){
  $do1->dodelete();
  $AppUI->setMsg("msg-CDocGed-error_file", UI_MSG_ERROR );
}
?>