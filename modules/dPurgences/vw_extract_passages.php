<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$page               = CValue::get('page', 0);
$debut_selection    = CValue::get("debut_selection", null);
$fin_selection      = CValue::get("fin_selection", null);
$type               = CValue::get("type", null);

$extractPassages = new CExtractPassages();

$where = array();
$where["group_id"]        = " = '".CGroups::loadCurrent()->_id."'";

if ($debut_selection) {
  $where["debut_selection"] = " >= '$debut_selection'";
}
if ($fin_selection) {
  $where["fin_selection"] = " <= '$fin_selection'";
}
if ($type) {
  $where["type"] = " = '$type'";
}

$order = "date_extract DESC";

$total_passages = $extractPassages->countList($where);
/** @var CExtractPassages[] $listPassages */
$listPassages   = $extractPassages->loadList($where, $order, "$page, 20");

$total_rpus = 0;
foreach ($listPassages as $_passage) {
  $_passage->loadRefsBack();
  $_passage->loadRefsFiles();

  $total_rpus += $_passage->_nb_rpus;
}

// Création du template
$smarty = new CSmartyDP("modules/dPurgences");
$smarty->assign("extractPassages", $extractPassages);
$smarty->assign("listPassages"   , $listPassages);

$smarty->assign("page"           , $page         );
$smarty->assign("debut_selection", $debut_selection);
$smarty->assign("fin_selection"  , $fin_selection);
$smarty->assign("type"           , $type);

$smarty->assign("total_passages" , $total_passages);

$smarty->assign("total_rpus"     , $total_rpus);

$smarty->display("vw_extract_passages.tpl");
