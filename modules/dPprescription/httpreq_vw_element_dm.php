<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_line_element_id = CValue::getOrSession("prescription_line_element_id");

$line = new CPrescriptionLineElement();
$line->load($prescription_line_element_id);
$line->loadRefDM();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("line", $line);
$smarty->display("inc_vw_element_dm.tpl");

?>