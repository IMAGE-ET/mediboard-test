<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$service             = CValue::getOrSession("service");
$web_service         = CValue::getOrSession("web_service"); 
$fonction            = CValue::getOrSession("fonction");
$service_demande     = CValue::get("service_demande");
$web_service_demande = CValue::get("web_service_demande");
$type                = CValue::get("type");

$web_services = array();
$functions    = array();
$ds = CSQLDataSource::get("std");

if ($type == "web_service") {
  $web_services = CMbArray::pluck($ds->loadList("SELECT web_service_name FROM echange_soap GROUP BY web_service_name"), "web_service_name");
  foreach ($web_services as $key => $_web_service) {
    $type_web_service = CMbArray::pluck($ds->loadList("SELECT `type` FROM echange_soap WHERE `web_service_name` = '$_web_service' LIMIT 1 "), "type");    
    if ($type_web_service[0] != $service_demande) {
      unset($web_services[$key]);
    }
  }
} else {
  $functions = CMbArray::pluck($ds->loadList("SELECT function_name FROM echange_soap GROUP BY function_name"), "function_name");
  foreach ($functions as $key => $_function) {
    $web_service_name = CMbArray::pluck($ds->loadList("SELECT `web_service_name` FROM echange_soap WHERE `function_name` = '$_function' LIMIT 1 "), "web_service_name");    
    if ($web_service_name[0] != $web_service_demande) {
      unset($functions[$key]);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('web_services', $web_services);
$smarty->assign('fonctions'   , $functions);
$smarty->assign("service"     , $service);
$smarty->assign("web_service" , $web_service);
$smarty->assign("fonction"    , $fonction);
$smarty->assign("type"        , $type);
$smarty->display("inc_filter_web_func.tpl");
?>