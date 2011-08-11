<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$consult_id = CValue::get("consult_id");
$consult = new CConsultation;
$consult->load($consult_id);

$smarty = new CSmartyDP;
$smarty->assign("consult", $consult);

$smarty->display("inc_accident_travail.tpl");

?>