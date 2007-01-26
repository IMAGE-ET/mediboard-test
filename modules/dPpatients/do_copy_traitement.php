<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

class CDoCopyTraitement extends CDoObjectAddEdit {
  function CDoCopyTraitement() {
    $this->CDoObjectAddEdit("CTraitement", "traitement_id");
    
    $this->createMsg = "Traitement créé";
    $this->modifyMsg = "Traitement modifié";
    $this->deleteMsg = "Traitement supprimé";
  }  
  
  function doBind() {
    parent::doBind();
    
    $object_class  = mbGetValueFromPost("object_class"  , null); 
    $object_id     = mbGetValueFromPost("object_id"     , null);
    
    unset($_POST["traitement_id"]);
    $this->_obj = $this->_objBefore;
    $this->_obj->_id = null;
    $this->_obj->traitement_id = null;
    $this->_obj->object_class  = $object_class;
    $this->_obj->object_id     = $object_id;
  }
}
$do = new CDoCopyTraitement;
$do->doIt();
?>