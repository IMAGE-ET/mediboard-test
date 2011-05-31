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

$ds = CSQLDataSource::get("std");

$module_orumip = CModule::getActive("orumip");
$orumip_active = $module_orumip && $module_orumip->mod_active;

$request = new CRequest;
$request->addSelect(array("code", "libelle"));

if ($orumip_active) {  
  $request->addTable("orumip_circonstance");
  $request->addWhere("libelle LIKE '%".addslashes($_keywords_circonstance)."%'");
}
else {
  $request->addTable("circonstance");
  $request->addWhere("code LIKE '%".addslashes($_keywords_circonstance)."%'");  
}

$circonstances = $ds->loadList($request->getRequest());

$smarty = new CSmartyDP;
$smarty->assign("circonstances", $circonstances);
$smarty->assign("_keywords_circonstance", $_keywords_circonstance);
$smarty->assign("orumip_active", $orumip_active);
$smarty->display("inc_circonstances_autocomplete.tpl");

?>