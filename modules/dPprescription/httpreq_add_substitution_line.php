<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI;

$prescription_line_medicament_id = mbGetValueFromGetOrSession("prescription_line_medicament_id");
$mode_pack = mbGetValueFromGet("mode_pack", "0");
$line = new CPrescriptionLineMedicament();
$line->load($prescription_line_medicament_id);

$user = new CMediusers();
$user->load($AppUI->user_id);
$is_praticien = $user->isPraticien();

// Chargement des lignes de substitutions de la ligne
$line->loadRefsSubstitutionLines();
$line->loadRefPrescription();

// Chargement des droits sur les lignes
foreach($line->_ref_substitution_lines as &$_line_sub){
  $_line_sub->getAdvancedPerms($is_praticien,"0"); 
  $_line_sub->loadRefsPrises();
  $_line_sub->loadRefParentLine();
}

$prescription =& $line->_ref_prescription;

// Chargement de la liste des moments
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("line", $line);
$smarty->assign("prescription", $prescription);
$smarty->assign("prescription_reelle", $prescription);
$smarty->assign("today", mbDate());
$smarty->assign("mode_pharma", 0);
$smarty->assign("prise_posologie", new CPrisePosologie());
$smarty->assign("moments", $moments);
$smarty->assign("mode_pack", $mode_pack);
$smarty->assign("full_line_guid", "");
$smarty->display("../../dPprescription/templates/inc_vw_add_substitution_line.tpl");

?>