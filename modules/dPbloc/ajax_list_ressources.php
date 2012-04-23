<?php

/**
 * dPbloc
 *  
 * @category dPbloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$ressource_id = CValue::getOrSession("ressource_id");

$ressource_materielle = new CRessourceMaterielle;

$where = array();
$where["ressource_materielle.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$ljoin = array();
$ljoin["type_ressource"] = "ressource_materielle.type_ressource_id = type_ressource.type_ressource_id";

$ressources_materielles = $ressource_materielle->loadList($where, "type_ressource.libelle", null, null, $ljoin);

CMbObject::massLoadFwdRef($ressources_materielles, "type_ressource_id");

foreach ($ressources_materielles as $_ressource) {
  $_ressource->loadRefTypeRessource();
}

$smarty = new CSmartyDP;

$smarty->assign("ressource_id", $ressource_id);
$smarty->assign("ressources_materielles", $ressources_materielles);

$smarty->display("inc_list_ressources.tpl");

?>