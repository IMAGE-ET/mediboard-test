<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$consult_id = CValue::get("consult_id");
$sejour_id  = CValue::get("sejour_id");

$consult = new CConsultation();
$consult->load($consult_id);
$consult->loadRefsFwd();

$sejour = new CSejour();
$sejour->load($sejour_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("consult", $consult);
$smarty->assign("sejour" , $sejour);
$smarty->display("inc_rpu_docitems.tpl");

?>