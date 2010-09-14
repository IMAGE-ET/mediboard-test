<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$service             = CValue::getOrSession("service");
$web_service         = CValue::getOrSession("web_service"); 
$fonction            = CValue::getOrSession("fonction");
$service_demande     = CValue::get("service_demande");
$web_service_demande = CValue::get("web_service_demande");
$type                = CValue::get("type");

$web_services = array();
$fonctions    = array();
$ds = CSQLDataSource::get("std");

if ($type == "web_service") {
	$res = $ds->query("SELECT web_service_name FROM echange_soap WHERE type = '$service_demande' GROUP BY web_service_name");
	while ($l = $ds->fetchAssoc($res)) {
		$web_services[] = $l['web_service_name'];
	}
} else {
	$res = $ds->query("SELECT function_name FROM echange_soap WHERE type = '$service_demande' AND web_service_name = '$web_service_demande' GROUP BY function_name");
	while ($l = $ds->fetchAssoc($res)) {
		$fonctions[] = $l['function_name'];
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('web_services', $web_services);
$smarty->assign('fonctions'   , $fonctions);
$smarty->assign("service"     , $service);
$smarty->assign("web_service" , $web_service);
$smarty->assign("fonction"    , $fonction);
$smarty->assign("type"        , $type);
$smarty->display("inc_filter_web_func.tpl");
?>