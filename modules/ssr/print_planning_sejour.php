<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date = CValue::getOrSession("date", mbDate());
$sejour_id = CValue::getOrSession("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefBilanSSR();
$bilan_ssr =& $sejour->_ref_bilan_ssr;
$bilan_ssr->loadRefTechnicien();
$technicien =& $bilan_ssr->_ref_technicien;
$technicien->loadRefKine();

// Chargement des evenement SSR 
$monday = mbDate("last monday", mbDate("+1 day", $date));
$sunday = mbDate("next sunday", mbDate("-1 DAY", $date));

$evenement_ssr = new CEvenementSSR();
$where = array();
$where["sejour_id"] = " = '$sejour_id'";
$where["debut"] = "BETWEEN '$monday 00:00:00' AND '$sunday 23:59:59'";
$evenements = $evenement_ssr->loadList($where);

$elements = array();
$intervenants = array();
foreach($evenements as $_evenement){
	$_evenement->loadRefElementPrescription();
	$_evenement->loadRefTherapeute();

  $elements[$_evenement->element_prescription_id] = $_evenement->_ref_element_prescription;
	$intervenants[$_evenement->element_prescription_id][$_evenement->therapeute_id] = $_evenement->_ref_therapeute;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("elements", $elements);
$smarty->assign("intervenants", $intervenants);
$smarty->assign("sejour", $sejour);
$smarty->display("print_planning_sejour.tpl");

?>