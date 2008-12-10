<?php /* $Id: vw_affectations.php 1059 2006-10-09 08:28:41Z maskas $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 1059 $
* @author Thomas Despoix
*/

global $can, $m;

CAppUI::requireModuleFile($m, "inc_vw_affectations");

$can->needsRead();

$date = mbGetValueFromGetOrSession("date", mbDate()); 
$afficher_chambres_cachees = mbGetValueFromGetOrSession("afficher_chambres_cachees");
$mode = mbGetValueFromGetOrSession("mode", 0);

// Chargement du service
$service = new CService;
$service->load(mbGetValueFromGet("service_id"));
$service->_vwService = 1;
loadServiceComplet($service, $date, $mode);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"        , $date );
$smarty->assign("demain"      , mbDate("+ 1 day", $date));
$smarty->assign("curr_service", $service);
$smarty->assign("afficher_chambres_cachees", $afficher_chambres_cachees);

$smarty->display("inc_affectations_services.tpl");

?>