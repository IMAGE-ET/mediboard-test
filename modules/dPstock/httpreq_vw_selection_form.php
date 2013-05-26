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

$selection_id = CValue::getOrSession('selection_id');

$selection = new CProductSelection();

if ($selection->load($selection_id)) {
  $selection->loadRefsBack();
  
  foreach ($selection->_back["selection_items"] as $_item) {
    $_item->updateFormFields();
  }
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('selection', $selection);
$smarty->display('inc_form_selection.tpl');
