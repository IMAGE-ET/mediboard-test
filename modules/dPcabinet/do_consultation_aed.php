<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getSystemClass("doobjectaddedit"));
require_once($AppUI->getModuleClass("dPcabinet", "consultation"));
require_once($AppUI->getModuleClass("dPcabinet", "consultAnesth"));

if ($chir_id = dPgetParam( $_POST, 'chir_id'))
  mbSetValueToSession('chir_id', $chir_id);

$do = new CDoObjectAddEdit("CConsultation", "consultation_id");
$do->createMsg = "Consultation créée";
$do->modifyMsg = "Consultation modifiée";
$do->deleteMsg = "Consultation supprimée";
$do->doBind();
if (intval(dPgetParam($_POST, 'del'))) {
  $consultAnesth = new CConsultAnesth;
  $where = array();
  $where["consultation_id"] = "= '".$do->_obj->consultation_id."'";
  $consultAnesth->loadObject($where);
  if($consultAnesth->consultation_anesth_id)
    $consultAnesth->delete();
  $do->doDelete();

} else {
  $do->doStore();
  if(isset($_POST["_dialog"]))
    $do->redirect = "m=dPcabinet&dialog=1&a=".$_POST["_dialog"];
  else
    $do->redirectStore = "m=dPcabinet&consultation_id=".$do->_obj->consultation_id;
  if(@$_POST["_operation_id"]) {
    $consultAnesth = new CConsultAnesth;
    $where = array();
    $where["consultation_id"] = "= '".$do->_obj->consultation_id."'";
    $consultAnesth->loadObject($where);
    $consultAnesth->consultation_id = $do->_obj->consultation_id;
    $consultAnesth->operation_id = $_POST["_operation_id"];
    $consultAnesth->store();
  }
}

$do->doRedirect();

?>
