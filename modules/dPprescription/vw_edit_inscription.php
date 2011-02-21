<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$datetime = CValue::getOrSession("datetime");
$prescription_id = CValue::getOrSession("prescription_id");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("datetime", $datetime);
$smarty->assign("prescription_id", $prescription_id);
$smarty->display("vw_edit_inscription.tpl");

?>