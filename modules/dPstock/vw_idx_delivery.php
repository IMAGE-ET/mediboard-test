<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m;

$can->needsAdmin();

$delivery_id  = mbGetValueFromGetOrSession('delivery_id');

// Loads the delivery
$delivery = new CProductDelivery();

// If delivery_id has been provided, we load the associated delivery
if ($delivery_id) {
  $delivery->load($delivery_id);
  $delivery->updateFormFields();
}

$classes_list = getInstalledClasses();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('delivery', $delivery);
$smarty->assign('classes_list', $classes_list);

$smarty->display('vw_idx_delivery.tpl');

?>