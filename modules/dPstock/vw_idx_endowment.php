<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$keywords     = CValue::getOrSession('keywords');
$start        = CValue::getOrSession('start');
$letter       = CValue::getOrSession('letter');

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('keywords', $keywords);
$smarty->assign('start', $start);
$smarty->assign('letter', $letter);

$smarty->display('vw_idx_endowment.tpl');
