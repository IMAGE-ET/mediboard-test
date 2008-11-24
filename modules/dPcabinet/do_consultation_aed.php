<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

if ($chir_id = mbGetValueFromPost("chir_id")) {
  mbSetValueToSession("chir_id", $chir_id);
}

$do = new CDoObjectAddEdit("CConsultation", "consultation_id");
$do->doBind();

if (intval(mbGetValueFromPost("del"))) {
    $do->doDelete();
    if(!$do->_obj->consultation_id){
      $selConsult = null;
      mbSetValueToSession("selConsult");
    }
} 
else {
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
  $_is_anesth = $ref_chir->isFromType(array("Anesthésiste"));

  if($_is_anesth && $do->_obj->patient_id){
    // Un Anesthesiste a été choisi
    $consultAnesth = new CConsultAnesth;
    $where = array();
    $where["consultation_id"] = "= '".$do->_obj->consultation_id."'";
    $consultAnesth->loadObject($where);
    $consultAnesth->consultation_id = $do->_obj->consultation_id;
    
    if(isset($_POST["_operation_id"])){
      $consultAnesth->operation_id = $_POST["_operation_id"];
      $consultAnesth->loadRefOperation();
    }
    
    // Remplissage du motif de pré-anesthésie si creation et champ motif vide
    if ($consultAnesth->_ref_operation->_id) {
    	$format_motif = CAppUI::conf('dPcabinet CConsultAnesth format_auto_motif');
    	$format_rques = CAppUI::conf('dPcabinet CConsultAnesth format_auto_rques');
    	
    	if (($format_motif && !$do->_obj->motif) || ($format_rques && !$do->_obj->rques)) {
	    	$op = $consultAnesth->_ref_operation;
	    	$op->loadRefChir();
	    	$op->_ref_chir->updateFormFields();
	    	$op->loadRefPlageOp();
	    	$op->loadRefSejour();
	    	
        $items = array(
          '%N' => $op->_ref_chir->_user_first_name,
          '%P' => $op->_ref_chir->_user_last_name,
          '%S' => substr($op->_ref_chir->_user_first_name, 0, 1).substr($op->_ref_chir->_user_last_name, 0, 1),
          '%L' => $op->libelle,
          '%I' => mbTransformTime(null, $op->_ref_plageop->date, CAppUI::conf('date')),
          '%E' => mbTransformTime(null, $op->_ref_sejour->entree_prevue, CAppUI::conf('date')),
          '%e' => mbTransformTime(null, $op->_ref_sejour->entree_prevue, CAppUI::conf('time')),
          '%T' => strtoupper(substr($op->_ref_sejour->type, 0, 1))
        );

	    	if ($format_motif && !$do->_obj->motif) {
	    		$do->_obj->motif = str_replace(array_keys($items), $items, $format_motif);
	    	}
	    	
    	  if ($format_rques && !$do->_obj->rques) {
          $do->_obj->rques = str_replace(array_keys($items), $items, $format_rques);
        }
	    	$do->_obj->store();
    	}
    }
    
    $consultAnesth->store();
  }
}

$do->doRedirect();

?>