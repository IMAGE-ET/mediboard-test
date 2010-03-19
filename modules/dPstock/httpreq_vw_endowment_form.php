<?php /* $Id: httpreq_vw_products_list.php 8116 2010-02-22 11:37:54Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8116 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$endowment_id = CValue::getOrSession('endowment_id');

$endowment = new CProductEndowment();

if ($endowment->load($endowment_id)) {
  $endowment->loadRefsBack();
  
  foreach($endowment->_back["endowment_items"] as $_item) {
    $_item->updateFormFields();
    $_item->_ref_product->loadRefStock();
  }
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('endowment', $endowment);
$smarty->display('inc_form_endowment.tpl');
