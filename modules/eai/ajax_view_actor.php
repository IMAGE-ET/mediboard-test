<?php
/**
 * Details interop receiver EAI
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$actor_guid  = CValue::getOrSession("actor_guid");

/** @var CInteropActor $actor */
$actor = CMbObject::loadFromGuid($actor_guid);
if ($actor->_id) {
  $actor->loadRefGroup();
  $actor->loadRefUser();
  $actor->loadRefObjectConfigs();

  if ($actor instanceof CInteropSender) {
    $actor->countBackRefs("routes");
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("actor" , $actor);
$smarty->display("inc_view_actor.tpl");