<?php /* $Id: $*/

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$_keywords_circonstance = CValue::post("_keywords_circonstance");

if ($_keywords_circonstance == "") {
  $_keywords_circonstance = "%%";
}

$module_orumip = CModule::getActive("orumip");
$orumip_active = $module_orumip && $module_orumip->mod_active;

$circonstances = array();

if ($orumip_active) {
  $circonstance = new COrumip;
  $circonstances = $circonstance->seek($_keywords_circonstance);
}
else {
  $circonstance = new CCirconstance;
  $circonstances = $circonstance->seek($_keywords_circonstance);
}

$smarty = new CSmartyDP;
$smarty->assign("circonstances", $circonstances);
$smarty->assign("_keywords_circonstance", $_keywords_circonstance);
$smarty->assign("orumip_active", $orumip_active);
$smarty->display("inc_circonstances_autocomplete.tpl");

?>