<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$item_id = mbGetValueFromGet('item_id');

// Loads the expected Order Item
$item = new CProductOrderItem();
$item->load($item_id);
$item->loadRefsBack();

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('curr_item', $item);

$smarty->display('inc_vw_order_reception_item.tpl');
?>
