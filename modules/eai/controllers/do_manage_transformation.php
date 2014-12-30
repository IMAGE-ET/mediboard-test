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

$transformation_id_move = CValue::post("transformation_id_move");
$direction              = CValue::post("direction");

$transf_rule = new CEAITransformation();
$transf_rule->load($transformation_id_move);

switch ($direction) {
  case "up":
    $transf_rule->rank--;
    break;

  case "down":
    $transf_rule->rank++;
    break;

  default:
}

$transf_to_move = new CEAITransformation();
$transf_to_move->actor_class = $transf_rule->actor_class;
$transf_to_move->actor_id    = $transf_rule->actor_id;
$transf_to_move->standard    = $transf_rule->standard;
$transf_to_move->rank        = $transf_rule->rank;
$transf_to_move->loadMatchingObject();

if ($transf_to_move->_id) {
  $direction == "up" ? $transf_to_move->rank++ : $transf_to_move->rank--;
  $transf_to_move->store();
}

$transf_rule->store();

/** @var CInteropActor $actor */
$actor = new $transf_rule->actor_class;
$actor->load($transf_rule->actor_id);

/** @var CEAITransformation[] $transformations */
$transformations = $actor->loadBackRefs("transformations", "rank");

$i = 1;
foreach ($transformations as $_transformation) {
  $_transformation->rank = $i;
  $_transformation->store();
  $i++;
}

CAppUI::stepAjax("CEAITransformation-msg-Move rank done");

CApp::rip();