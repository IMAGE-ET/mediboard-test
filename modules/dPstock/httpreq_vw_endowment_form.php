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

$endowment_id = CValue::getOrSession('endowment_id');

$endowment = new CProductEndowment();

if ($endowment->load($endowment_id)) {
  $items = $endowment->loadRefsEndowmentItems();
  $endowment->loadRefsNotes();
  
  foreach ($items as $_item) {
    $_item->updateFormFields();
    $_item->_ref_product->loadRefStock();
  }
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('endowment', $endowment);
$smarty->display('inc_form_endowment.tpl');
