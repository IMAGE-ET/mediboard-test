<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */

global $can;
$can->needsRead();

$track = array();
$orderby = 'date_dispensation DESC';

$product = new CProduct();
$product->loadList();

$list = new CProductDelivery;
$list = $list->loadList(null, $orderby);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('list', $list);
$smarty->display('vw_traceability.tpl');

?>