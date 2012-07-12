<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$endowment_id = CValue::get('endowment_id');

$endowment = new CProductEndowment();
$endowment->load($endowment_id);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('endowment', $endowment);

$smarty->display('inc_duplicate_endowment.tpl');
