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
$type_ressource_id = CValue::get("type_ressource_id");

$ressource_materielle = new CRessourceMaterielle;
$ressource_materielle->load($ressource_id);

if (!$ressource_materielle->_id) {
  $ressource_materielle->group_id = CGroups::loadCurrent()->_id;
  $ressource_materielle->type_ressource_id = $type_ressource_id;
}

$ressource_materielle->loadRefTypeRessource();

$smarty = new CSmartyDP;

$smarty->assign("ressource_materielle", $ressource_materielle);

$smarty->display("inc_edit_ressource.tpl");

?>