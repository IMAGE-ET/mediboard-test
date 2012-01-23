<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::get("sejour_id");

$prescription = new CPrescription;
$prescription->object_class = "CSejour";
$prescription->object_id = $sejour_id;
$prescription->type = "sejour";

$prescription->loadMatchingObject();
$prescription->loadRefsLinesElement("0","consult", 1, "", "", "", 1);

$lines = $prescription->_ref_prescription_lines_element;

foreach ($lines as $_line) {
  $_line->loadRefsPrises();
	$_line->_prise_id = count($_line->_ref_prises) ? reset($_line->_ref_prises)->_id : '';
}

$user = CAppUI::$user;

$administration = new CAdministration;
$administration->administrateur_id = $user->_id;
$administration->quantite = 1;
$administration->_date = mbDate();
$administration->_time = mbTime();
$smarty = new CSmartyDP;

$smarty->assign("lines", $lines);
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("prescription_id", $prescription->_id);
$smarty->assign("isAnesth", $user->isAnesth());
$smarty->assign("administration", $administration);

$smarty->display("inc_administration_for_consult.tpl");

?>