<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m;

$can->needsRead();

$delivery_id  = mbGetValueFromGetOrSession('delivery_id');

// Loads the delivery
$delivery = new CProductDelivery();

// If stock_id has been provided, we load the associated product
if ($delivery_id) {
  $delivery->_id = $delivery_id;
  if (!$delivery->loadMatchingObject()) {
  	$delivery = new CProductDelivery();
  }
  $delivery->updateFormFields();
}

$classes_list = getInstalledClasses();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('delivery', $delivery);
$smarty->assign('classes_list', $classes_list);

$smarty->display('vw_idx_delivery.tpl');

?>