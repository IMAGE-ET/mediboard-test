<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

class CDoValidationRepasAddEdit extends CDoObjectAddEdit {
  function CDoValidationRepasAddEdit() {
    $this->CDoObjectAddEdit("CValidationRepas", "validationrepas_id");
  }
  
  function doStore() {
    $this->_obj->resetModifications();
    parent::doStore();
  }
}
$do = new CDoValidationRepasAddEdit;
$do->doIt();
?>