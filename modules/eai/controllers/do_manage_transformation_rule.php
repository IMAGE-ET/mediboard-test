<?php

/**
 * Actor domain aed
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$transformation_rule_id_move = CValue::post("transformation_rule_id_move");
$direction                   = CValue::post("direction");

$transf_rule = new CEAITransformationRule();
$transf_rule->load($transformation_rule_id_move);

$transf_rule_to_move = new CEAITransformationRule;
$transf_rule_to_move->eai_transformation_ruleset_id = $transf_rule->eai_transformation_ruleset_id;

if ($direction == "up"   && $transf_rule->rank == 1 ||
    $direction == "down" && $transf_rule->rank == $transf_rule_to_move->countMatchingList()
) {
  CApp::rip();
}

switch ($direction) {
  case "up":
    $transf_rule->rank--;
    break;

  case "down":
    $transf_rule->rank++;
    break;

  default:
}

$transf_rule_to_move = new CEAITransformationRule;
$transf_rule_to_move->eai_transformation_ruleset_id = $transf_rule->eai_transformation_ruleset_id;
$transf_rule_to_move->rank = $transf_rule->rank;
$transf_rule_to_move->loadMatchingObject();

if ($transf_rule_to_move->_id) {
  $direction == "up" ? $transf_rule_to_move->rank++ : $transf_rule_to_move->rank--;
  $transf_rule_to_move->store();
}

$transf_rule->store();

CAppUI::stepAjax("eai-msg-CEAITransformationRule--Move rank done");

CApp::rip();