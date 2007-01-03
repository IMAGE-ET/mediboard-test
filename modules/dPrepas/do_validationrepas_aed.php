<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;

class CDoValidationRepasAddEdit extends CDoObjectAddEdit {
  function CDoValidationRepasAddEdit() {
    $this->CDoObjectAddEdit("CValidationRepas", "validationrepas_id");
    $this->createMsg = "Validation des repas créée";
    $this->modifyMsg = "Validation des repas modifiée";
    $this->deleteMsg = "Validation des repas supprimée";
  }
  
  function doStore() {
    
    $this->_obj->resetModifications();
    parent::doStore();
  }
  
}
$do = new CDoValidationRepasAddEdit;
$do->doIt();
?>