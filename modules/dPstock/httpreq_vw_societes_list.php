<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
 
CCanDo::checkRead();

$start    = CValue::get('start', 0);
$keywords = CValue::get('keywords');

$suppliers = CValue::get('suppliers');
$manufacturers = CValue::get('manufacturers');
$inactive  = CValue::get('inactive');

CValue::setSession('suppliers', $suppliers);
CValue::setSession('manufacturers', $manufacturers);
CValue::setSession('inactive', $inactive);

if (!$keywords) {
  $keywords = "%";
}

$societe = new CSociete();
$list = $societe->seek($keywords, null, 1000, true);
$list_count = $societe->_totalSeek;

foreach ($list as $_id => $_societe) {
  if (!($manufacturers && $_societe->_is_manufacturer || 
        $suppliers && $_societe->_is_supplier ||
        $inactive && (!$_societe->_is_supplier && !$_societe->_is_manufacturer))) {
    unset($list[$_id]);
    $list_count--;
  }
  else {
    $_societe->countBackRefs("products");
    $_societe->countBackRefs("product_references");
  }
}

$list = array_slice($list, $start, 30);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list',       $list);
$smarty->assign('list_count', $list_count);
$smarty->assign('start',      $start);

$smarty->display('inc_societes_list.tpl');

