<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPhospi", "service"));

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Récupération du service à ajouter/editer
$serviceSel = new CService;
$serviceSel->load(mbGetValueFromGetOrSession("service_id"));

// Récupération des services
$order = "nom";
$services = new CService;
$services = $services->loadList(null, $order);

// Création du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('serviceSel', $serviceSel);
$smarty->assign('services', $services);

$smarty->display('vw_idx_services.tpl');

?>