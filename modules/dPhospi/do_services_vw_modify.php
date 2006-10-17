<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m, $g;

$m = "dPhospi";

// Récupération des chambres/services
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$services = $services->loadList($where);

// Affichage ou non des services
$defaultVwService = array();
$vwService = mbGetValueFromPostOrSession("vwService", array());
foreach ($services as $service_id => $service) {
  $defaultVwService[$service_id] = 1;
}
$vwService = $vwService + $defaultVwService;
mbSetValueToSession("vwService", $vwService);

$AppUI->setMsg("Préférences prises en compte", UI_MSG_OK);

echo $AppUI->getMsg();

exit(0);

?>