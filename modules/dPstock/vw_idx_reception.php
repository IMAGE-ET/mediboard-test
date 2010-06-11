<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$start = intval(CValue::get("start", 0));

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("start", $start);
$smarty->display('vw_idx_reception.tpl');
