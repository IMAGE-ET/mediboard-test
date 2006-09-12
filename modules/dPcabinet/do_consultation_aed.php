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

if ($chir_id = mbGetValueFromPost("chir_id")) {
  mbSetValueToSession("chir_id", $chir_id);
}

$do = new CDoObjectAddEdit("CConsultation", "consultation_id");
$do->createMsg = "Consultation créée";
$do->modifyMsg = "Consultation modifiée";
$do->deleteMsg = "Consultation supprimée";
$do->doBind();
if (intval(mbGetValueFromPost("del"))) {
    $do->doDelete();
} else {
  $do->doStore();
  if(isset($_POST["_dialog"]))
    $do->redirect = "m=dPcabinet&dialog=1&a=".$_POST["_dialog"];
  else
    $do->redirectStore = "m=dPcabinet&consultation_id=".$do->_obj->consultation_id;
  
  // Le Praticien choisi est-il un anesthesiste
  $ref_plageconsult = new CPlageconsult;
  $ref_plageconsult->load($do->_obj->plageconsult_id);
  $ref_plageconsult->loadRefsFwd();
  $ref_chir = $ref_plageconsult->_ref_chir;
  if($ref_chir->isFromType(array("Anesthésiste"))) {
    $_is_anesth=true; 
  } else {
    $_is_anesth=false;
  } 
  if($_is_anesth){
    // Un Anesthesiste a été choisi
    $consultAnesth = new CConsultAnesth;
    $where = array();
    $where["consultation_id"] = "= '".$do->_obj->consultation_id."'";
    $consultAnesth->loadObject($where);
    $consultAnesth->consultation_id = $do->_obj->consultation_id;
    if(@$_POST["_operation_id"]) {
      $consultAnesth->operation_id = $_POST["_operation_id"];
    }else{
      $consultAnesth->operation_id = "";
    }
    $consultAnesth->store();
  }
}

$do->doRedirect();

?>