<?php
/**
 * Link transformation rule
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$event_class   = CValue::getOrSession("event_class");
$message_class = CValue::getOrSession("message_class");
$actor_guid    = CValue::getOrSession("actor_guid");

/** @var CInteropActor $actor */
$actor = CMbObject::loadFromGuid($actor_guid);

/** @var CInteropNorm $message */
$message = new $message_class;

$event = null;
$where = array();

if ($event_class) {
  $event = new $event_class;

  $where[] = "message IS NULL OR message = '$event_class'";
}

/** @var CEAITransformation[] $transformations */
$transformations = $actor->loadRefsEAITransformation($where);
foreach ($transformations as $_transformation) {
  $_transformation->loadRefEAITransformationRule();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("actor"          , $actor);
$smarty->assign("event"          , $event);
$smarty->assign("message"        , $message);
$smarty->assign("transformations", $transformations);

$smarty->display("inc_list_transformations.tpl");