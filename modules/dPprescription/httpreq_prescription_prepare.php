<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$line_id_for_poso = CValue::get("line_id_for_poso");
$code_cip = CValue::get("code_cip");
$element_prescription_id = CValue::get("element_prescription_id");
$prescription_line_guid = CValue::get("prescription_line_guid");

// Chargement de la ligne de prescription
$prescription_line = CMbObject::loadFromGuid($prescription_line_guid);

if($line_id_for_poso){
	// Chargement de la ligne dont les prises sont à dupliquer
	if($code_cip){
	  $line_for_poso = new CPrescriptionLineMedicament();
	} else {
	  $line_for_poso = new CPrescriptionLineElement();
  }
  $line_for_poso->load($line_id_for_poso);
  $line_for_poso->loadRefsPrises();
	
  foreach($line_for_poso->_ref_prises as $_prise){
    $_prise->_id = '';
    $_prise->object_id = $prescription_line->_id;
    $_prise->store();
  }
}

if(!$prescription_line->debut){
  $prescription_line->debut = mbDate();
}
$prescription_line->loadRefsPrises();
$prescription_line->countPrisesLine();
$prescription_line->loadRefsFwd();

if($prescription_line instanceof CPrescriptionLineMedicament){
	$type = "Med";
} else {
	$type = $prescription_line->_chapitre;
}

// Chargement des moments unitaires
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("type", $type);
$smarty->assign("line", $prescription_line);
$smarty->assign("moments", $moments);
$smarty->assign("typeDate",$type);
$smarty->display("../../dPprescription/templates/line/inc_vw_prises_posologie.tpl");

?>