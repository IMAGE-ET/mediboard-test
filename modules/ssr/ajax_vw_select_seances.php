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
$therapeute_id = CValue::get("therapeute_id");
$equipement_id = CValue::get("equipement_id");
$prescription_line_element_id = CValue::get("prescription_line_element_id");

$monday = mbDate("last monday", mbDate("+1 day", $date));
$sunday = mbDate("next sunday", mbDate("-1 DAY", $date));

// Chargement de la ligne
$line_element = new CPrescriptionLineElement();
$line_element->load($prescription_line_element_id);
$element_prescription_id = $line_element->element_prescription_id;

// Chargement des seances en fonction des parametres selectionnés
$seance = new CEvenementSSR();
$ljoin = array();
$ljoin[] = "evenement_ssr AS evt_seance ON (evt_seance.seance_collective_id = evenement_ssr.evenement_ssr_id)";
$ljoin["prescription_line_element"] = "evt_seance.prescription_line_element_id = prescription_line_element.prescription_line_element_id";

$where = array();
$where["evenement_ssr.sejour_id"] = " IS NULL";
$where["evenement_ssr.debut"] = "BETWEEN '$monday 00:00:00' AND '$sunday 23:59:59'";
$where["evenement_ssr.therapeute_id"] = " = '$therapeute_id'";
$where["evenement_ssr.equipement_id"] = " = '$equipement_id'";
$where["prescription_line_element.element_prescription_id"] = " = '$element_prescription_id'";

$seances = $seance->loadList($where, null, null, null, $ljoin);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("seances", $seances);
$smarty->display("inc_vw_select_seance.tpl");

?>