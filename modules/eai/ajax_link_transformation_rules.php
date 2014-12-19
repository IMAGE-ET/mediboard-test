<?php
/**
 * Edit transformaiton rule EAI
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$actor_guid        = CValue::getOrSession("actor_guid");
$event_name        = CValue::getOrSession("event_name");

/** @var CInteropActor $actor */
$actor = CMbObject::loadFromGuid($actor_guid);

$event = new $event_name;

$transformation = new CEAITransformation();

// On charge la liste des règles possibles en fonction des propriétés de l'évènement
$transf_rule = new CEAITransformationRule();
//  $transf_rule->bindObject(($event));
$transf_rules = $transf_rule->loadMatchingList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("actor"         , $actor);
$smarty->assign("event"         , $event);
$smarty->assign("transf_rules"  , $transf_rules);
$smarty->assign("transformation", $transformation);

$smarty->display("inc_link_transformation_rules.tpl");