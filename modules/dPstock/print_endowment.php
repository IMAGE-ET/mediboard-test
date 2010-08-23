<?php /* $Id: vw_idx_stock_location.php 7809 2010-01-12 15:05:03Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7809 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$endowment_id = CValue::get('endowment_id');

$endowment = new CProductEndowment();
$endowment->load($endowment_id);
$endowment->loadRefsFwd();
$endowment->updateFormFields();
$endowment->loadRefsBack();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('endowment', $endowment);
$smarty->display('print_endowment.tpl');

?>