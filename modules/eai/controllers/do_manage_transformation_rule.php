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

switch ($direction) {
  case "up":
    $transf_rule->rank--;
    break;

  case "down":
    $transf_rule->rank++;
    break;

  default:
}

$transf_rule_to_move = new CEAITransformationRule();
$transf_rule_to_move->eai_transformation_ruleset_id = $transf_rule->eai_transformation_ruleset_id;
$transf_rule_to_move->rank                          = $transf_rule->rank;
$transf_rule_to_move->loadMatchingObject();

if ($transf_rule_to_move->_id) {
  $direction == "up" ? $transf_rule_to_move->rank++ : $transf_rule_to_move->rank--;
  $transf_rule_to_move->store();
}

$transf_rule->store();

/** @var CEAITransformationRuleSet $actor */
$transf_ruleset = new CEAITransformationRuleSet();
$transf_ruleset->load($transf_rule->eai_transformation_ruleset_id);

/** @var CEAITransformationRule[] $transformation_rules */
$transformation_rules = $transf_ruleset->loadBackRefs("eai_transformation_rules", "rank");

$i = 1;
foreach ($transformation_rules as $_trans_rule) {
  $_trans_rule->rank = $i;
  $_trans_rule->store();
  $i++;
}

CAppUI::stepAjax("CEAITransformationRule-msg-Move rank done");

CApp::rip();