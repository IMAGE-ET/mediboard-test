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

$start = intval(CValue::get("start", 0));

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("start", $start);
$smarty->display('vw_idx_reception.tpl');
