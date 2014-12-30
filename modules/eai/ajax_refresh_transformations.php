<?php
/**
 * Refresh transformations
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$event_name = CValue::getOrSession("event_name");
$actor_guid = CValue::getOrSession("actor_guid");

/** @var CInteropActor $actor */
$actor = CMbObject::loadFromGuid($actor_guid);

$event = new $event_name;

// Chargement des transformations peut-être existantes pour cet évènement
$transformation = new CEAITransformation();
$transformation->bindObject($event, $actor);
$transformations = $transformation->loadMatchingList("rank");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("actor"          , $actor);
$smarty->assign("event_name"     , $event_name);
$smarty->assign("transformations", $transformations);

$smarty->display("inc_list_transformations_lines.tpl");