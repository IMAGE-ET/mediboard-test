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

$keywords     = CValue::getOrSession('keywords');
$start        = CValue::getOrSession('start');
$letter       = CValue::getOrSession('letter');

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('keywords', $keywords);
$smarty->assign('start', $start);
$smarty->assign('letter', $letter);

$smarty->display('vw_idx_selection.tpl');
