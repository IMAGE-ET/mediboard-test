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
 
CCanDo::checkEdit();

$tabs = array(
  'vw_idx_societe', 
  'vw_idx_stock_location',
  'vw_idx_selection', 
  'vw_idx_endowment',
  'vw_idx_category', 
);

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("tabs", $tabs);
$smarty->display('vw_idx_setup.tpl');

