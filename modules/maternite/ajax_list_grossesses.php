<?php

/**
 * Liste des grossesses pour une parturiente
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$parturiente_id = CValue::getOrSession("parturiente_id");
$object_guid    = CValue::getOrSession("object_guid");
$show_checkbox  = CValue::get("show_checkbox");

$object = new CMbObject();
if ($object_guid) {
  $object = CMbObject::loadFromGuid($object_guid);
}

$grossesse = new CGrossesse();
$grossesse->parturiente_id = $parturiente_id;
$grossesses = $grossesse->loadMatchingList("terme_prevu DESC, active DESC");

CMbObject::massCountBackRefs($grossesses, "sejours");
CMbObject::massCountBackRefs($grossesses, "consultations");
CMbObject::massCountBackRefs($grossesses, "naissances");

$smarty = new CSmartyDP();

$smarty->assign("grossesses"   , $grossesses);
$smarty->assign("object"       , $object);
$smarty->assign("show_checkbox", $show_checkbox);

$smarty->display("inc_list_grossesses.tpl");
