<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$object_id    = mbGetValueFromGet("object_id");
$object_class = mbGetValueFromGet("object_class");

$mode_pack = mbGetValueFromGet("mode_pack", "0");

$object = new $object_class;
$object->load($object_id);

$user = new CMediusers();
$user->load($AppUI->user_id);
$is_praticien = $user->isPraticien();

// Chargement des lignes de substitutions de la ligne
$object->loadRefsSubstitutionLines();
$object->loadRefPrescription();

// Chargement des droits sur les lignes
foreach($object->_ref_substitution_lines as $_lines){
	foreach($_lines as $_line_sub){
	  if($_line_sub->_class_name == "CPrescriptionLineMedicament"){
	    $_line_sub->loadRefsFwd();
	    $_line_sub->loadRefsPrises();
	  } else {
	    $_line_sub->loadRefsLines();
	    foreach($_line_sub->_ref_lines as $_line_perf){
	      $_line_perf->loadRefsFwd();
	    }
	  }
	  
	  $_line_sub->getAdvancedPerms($is_praticien,"0"); 
    $_line_sub->loadRefParentLine();  
	}
}
$prescription =& $object->_ref_prescription;

// Chargement de toutes les perfusions qui ne sont pas actives
$prescription->loadRefsPerfusions(0,null,0);
foreach($prescription->_ref_perfusions as $_perfusion){
	$_perfusion->loadRefsLines();
}
// Chargement de la liste des moments
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

// Chargement des aides
$prescriptionLineMedicament = new CPrescriptionLineMedicament();
$perfusion = new CPerfusion();
$prescriptionLineMedicament->loadAides($AppUI->user_id);
$aides_prescription[$AppUI->user_id]["CPrescriptionLineMedicament"] = $prescriptionLineMedicament->_aides["commentaire"]["no_enum"];
$perfusion->loadAides($AppUI->user_id);
$aides_prescription[$AppUI->user_id]["CPerfusion"] = $perfusion->_aides["commentaire"]["no_enum"];


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("aides_prescription", $aides_prescription);
$smarty->assign("line", $object);
$smarty->assign("prescription", $prescription);
$smarty->assign("prescription_reelle", $prescription);
$smarty->assign("today", mbDate());
$smarty->assign("mode_pharma", 0);
$smarty->assign("prise_posologie", new CPrisePosologie());
$smarty->assign("perfusion", new CPerfusion());
$smarty->assign("moments", $moments);
$smarty->assign("mode_pack", $mode_pack);
$smarty->assign("full_line_guid", "");
$smarty->assign("now", mbDateTime());
$smarty->display("../../dPprescription/templates/inc_vw_add_substitution_line.tpl");

?>