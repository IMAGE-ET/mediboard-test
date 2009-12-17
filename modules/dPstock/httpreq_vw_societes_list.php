<?php /* $Id: httpreq_vw_products_list.php 7403 2009-11-23 15:42:32Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7403 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsRead();

$start    = CValue::get('start', 0);
$keywords = CValue::get('keywords');

$societe = new CSociete();
$list_count = $societe->countList();
$list = $societe->loadList(null, "name", intval($start).",25");

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list',       $list);
$smarty->assign('list_count', $list_count);
$smarty->assign('start',      $start);

$smarty->display('inc_societes_list.tpl');
?>
