<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$sejour_id = CValue::getOrSession("sejour_id");

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefPrescriptionSejour();
$sejour->_ref_prescription_sejour->countBackRefs("prescription_line_element");

// Recherche des sejours SSR du patient
$where = array();
$where["patient_id"] = " = '$sejour->patient_id'";
$where["type"] = " = 'ssr'";
$where["annule"] = " = '0'";
$where["sejour_id"] = " != '$sejour->_id'";
$where["sortie"] = " <= '".mbDateTime()."'";

$sejours = new CSejour();
$sejours = $sejours->loadList($where);

foreach($sejours as $_sejour){
	$_sejour->loadRefBilanSSR();
	$_sejour->_ref_bilan_ssr->loadRefPraticienDemandeur();
	
	$_sejour->loadRefPrescriptionSejour();
	$_sejour->_ref_prescription_sejour->loadRefsLinesElementByCat();
  $_sejour->_ref_prescription_sejour->countRecentModif();
}

$colors = CColorLibelleSejour::loadAllFor(CMbArray::pluck($sejours, "libelle"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("sejours", $sejours);
$smarty->assign("colors", $colors);
$smarty->display("inc_vw_sejours_patient.tpl");

?>