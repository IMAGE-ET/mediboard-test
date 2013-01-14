<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id   = CValue::get("sejour_id");
$date        = CValue::get("date");
$default_tab = CValue::get("default_tab", "dossier_traitement");
$popup       = CValue::get("popup", 0);
$operation_id = CValue::get("operation_id");
$mode_pharma  = CValue::get("mode_pharma", 0);

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefPrescriptionSejour();

$smarty = new CSmartyDP;
$smarty->assign("sejour", $sejour);
$smarty->assign("date"  , $date);
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("isPrescriptionInstalled" , CModule::getActive("dPprescription"));
$smarty->assign("default_tab", $default_tab);
$smarty->assign("popup", $popup);
$smarty->assign("operation_id", $operation_id);
$smarty->assign("mode_pharma", $mode_pharma);
$smarty->display("inc_dossier_sejour.tpl");

?>