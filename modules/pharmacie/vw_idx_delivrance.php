<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$delivrance_id = mbGetValueFromGetOrSession('delivrance_id');

$delivrance = new CDelivrance();
$delivrance->load($delivrance_id);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('delivrance', $delivrance);

$smarty->display('vw_idx_delivrance.tpl');

?>