<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/


CCanDo::checkRead();

global $m;
CAppUI::requireModuleFile($m, "inc_vw_affectations");

$date       = CValue::getOrSession("date", CMbDT::date());
$mode       = CValue::getOrSession("mode", 0);
$service_id = CValue::get("service_id");

// Chargement du service
$service = new CService();
$service->load($service_id);
loadServiceComplet($service, $date, $mode);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"        , $date );
$smarty->assign("demain"      , CMbDT::date("+ 1 day", $date));
$smarty->assign("curr_service", $service);

$smarty->display("inc_affectations_services.tpl");

?>