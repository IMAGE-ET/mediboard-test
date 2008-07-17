<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author Fabien Ménager
 */

global $can, $g;
$can->needsRead();

$stock_id         = mbGetValueFromGetOrSession('stock_id');
$category_id      = mbGetValueFromGetOrSession('category_id');
$service_id       = mbGetValueFromGetOrSession('service_id');

// Categories list
$list_categories = new CProductCategory();
$list_categories = $list_categories->loadList(null, 'name');

// Services list
$where = array('group_id' => "= $g");
$list_services = new CService();
$list_services = $list_services->loadList($where, 'nom');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('category_id', $category_id);
$smarty->assign('service_id',  $service_id);

$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_services',   $list_services);

$smarty->display('vw_idx_delivrance.tpl');

?>