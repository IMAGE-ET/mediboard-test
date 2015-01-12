<?php

/**
 * Link transformations
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$actor_guid           = CValue::post("actor_guid");
$event_name           = CValue::post("event_name");
$transformation_rules = CValue::post("transformation_rules", array());

/** @var CInteropActor $actor */
$actor = CMbObject::loadFromGuid($actor_guid);

$event = new $event_name;

// Ajout des transformations à l'acteur
foreach ($transformation_rules as $_transf_rule_id) {
  $transformation_rule = new CEAITransformationRule();
  $transformation_rule->load($_transf_rule_id);

  $transformation = new CEAITransformation();
  $transformation->bindTransformationRule($transformation_rule, $actor);
  $transformation->message = $event_name;

  if ($msg = $transformation->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
  else {
    CAppUI::setMsg("CEAITransformation-msg-modify");
  }
}

echo CAppUI::getMsg();
CApp::rip();



