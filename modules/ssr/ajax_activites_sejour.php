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

// Prescription
$sejour->loadRefPrescriptionSejour();
$prescription =& $sejour->_ref_prescription_sejour;
$prescription->loadRefsLinesElementByCat();

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

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour" , $sejour);
$smarty->assign("bilan"  , $bilan);
$smarty->assign("plateau", $plateau);
$smarty->assign("prescription", $prescription);
$smarty->display("inc_activites_sejour.tpl");

?>