<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

class CDoCopyAddiction extends CDoObjectAddEdit {
  function CDoCopyAddiction() {
    $this->CDoObjectAddEdit("CAddiction", "addiction_id");
    
    $this->createMsg = "Addiction créée";
    $this->modifyMsg = "Addiction modifiée";
    $this->deleteMsg = "Addiction supprimée";
  }  
  
  function doBind() {
    parent::doBind();
    
    $object_class  = mbGetValueFromPost("object_class"  , null); 
    $object_id     = mbGetValueFromPost("object_id"     , null);
    
    unset($_POST["addiction_id"]);
    $this->_obj = $this->_objBefore;
    $this->_obj->_id = null;
    $this->_obj->antecedent_id = null;
    $this->_obj->object_class  = $object_class;
    $this->_obj->object_id     = $object_id;
  }
}
$do = new CDoCopyAddiction;
$do->doIt();
?>