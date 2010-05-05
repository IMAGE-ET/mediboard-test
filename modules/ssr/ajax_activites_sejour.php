<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

// Sejour SSR
$sejour = new CSejour;
$sejour->load(CValue::get("sejour_id"));
$sejour->loadRefPatient();

$date = CValue::getOrSession("date", mbDate());

$monday = mbDate("last monday", mbDate("+1 day", $date));
$sunday = mbDate("next sunday", $date);

for ($i = 0; $i < 7; $i++) {
	$_date = mbDate("+$i day", $monday);
  $list_days[$_date] = mbTransformTime(null, $_date, "%a");
}

// Prescription
$sejour->loadRefPrescriptionSejour();
$prescription =& $sejour->_ref_prescription_sejour;
$prescription->loadRefsLinesElementByCat();

// Chargements des codes cdarrs des elements de prescription
foreach ($prescription->_ref_prescription_lines_element_by_cat as $_lines_by_chap){
  foreach ($_lines_by_chap as $_lines_by_cat){
    foreach ($_lines_by_cat['element'] as $_line){
    	$_line->_ref_element_prescription->loadBackRefs("cdarrs");
    }
	}
}

// Bilan
$sejour->loadRefBilanSSR();
$bilan =& $sejour->_ref_bilan_ssr;

// Technicien et plateau
$technicien = new CTechnicien;
$plateau = new CPlateauTechnique;
if ($technicien->kine_id = $bilan->kine_id) {
	$technicien->loadMatchingObject();
	$plateau = $technicien->loadFwdRef("plateau_id");
	$plateau->loadRefsEquipements();
  $plateau->loadRefsTechniciens();
}

// Chargement de tous les plateaux et des equipements et techniciens associés
$plateau_tech = new CPlateauTechnique();
$plateau_tech->group_id = CGroups::loadCurrent()->_id;
$plateaux = $plateau_tech->loadMatchingList();
foreach($plateaux as $_plateau_tech){
	$_plateau_tech->loadRefsEquipements();
	$_plateau_tech->loadRefsTechniciens();
}

$evenement_ssr = new CEvenementSSR();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("evenement_ssr", $evenement_ssr);
$smarty->assign("list_days", $list_days);
$smarty->assign("sejour" , $sejour);
$smarty->assign("bilan"  , $bilan);
$smarty->assign("plateau", $plateau);
$smarty->assign("prescription", $prescription);
$smarty->assign("plateaux", $plateaux);
$smarty->display("inc_activites_sejour.tpl");

?>