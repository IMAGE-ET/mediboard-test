<?php /* $Id: httpreq_vw_products_list.php 8116 2010-02-22 11:37:54Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8116 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$selection_id = CValue::getOrSession('selection_id');

$selection = new CProductSelection();

if ($selection->load($selection_id)) {
  $selection->loadRefsBack();
  
  foreach($selection->_back["selection_items"] as $_item) {
    $_item->updateFormFields();
  }
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('selection', $selection);
$smarty->display('inc_form_selection.tpl');
