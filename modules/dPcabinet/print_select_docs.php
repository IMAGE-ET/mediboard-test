<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::check();

global $can;

$consultation_id = CValue::get("consultation_id");
$sejour_id       = CValue::get("sejour_id");

$count_prescription = 0;
if(CModule::getActive("dPprescription")){
	// Chargement de la prescription de pre-admission
	$prescription_preadm = new CPrescription();
	$prescription_sejour = new CPrescription();
	$prescription_sortie = new CPrescription();
	
	if($sejour_id){
		$prescription_sortie->object_id = $prescription_sejour->object_id = $prescription_preadm->object_id = $sejour_id;
		$prescription_sortie->object_class = $prescription_sejour->object_class = $prescription_preadm->object_class = "CSejour";
	  
		$prescription_preadm->type = "pre_admission";
		$prescription_preadm->loadMatchingObject();
		if($prescription_preadm->_id){
			$count_prescription++;
		}
		$prescription_sejour->type = "sejour";
		$prescription_sejour->loadMatchingObject();
		if($prescription_sejour->_id){
      $count_prescription++;
    }
		$prescription_sortie->type = "sortie";
		$prescription_sortie->loadMatchingObject();
		if($prescription_sortie->_id){
      $count_prescription++;
    }
  }
}

// Consultation courante
$consult = new CConsultation();
$consult->load($consultation_id);
$can->edit &= $consult->canEdit();

$can->needsEdit();
$can->needsObject($consult);

$consult->loadRefsDocs();  

// Cr�ation du template
$smarty = new CSmartyDP();
if(CModule::getActive("dPprescription")){
  $smarty->assign("prescription_preadm" , $prescription_preadm);
  $smarty->assign("prescription_sejour" , $prescription_sejour);
  $smarty->assign("prescription_sortie" , $prescription_sortie);
}
$smarty->assign("count_prescription"  , $count_prescription);
$smarty->assign("consult"             , $consult);
$smarty->assign("documents"           , $consult->_ref_documents);
$smarty->display("print_select_docs.tpl");
?>
