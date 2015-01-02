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

$transformation = new CEAITransformation();
$transformation->load($transformation_id_move);

switch ($direction) {
  case "up":
    $transformation->rank--;
    break;

  case "down":
    $transformation->rank++;
    break;

  default:
}

$transf_to_move = new CEAITransformation();
$transf_to_move->actor_class = $transformation->actor_class;
$transf_to_move->actor_id    = $transformation->actor_id;
$transf_to_move->rank        = $transformation->rank;
$transf_to_move->loadMatchingObject();

if ($transf_to_move->_id) {
  $direction == "up" ? $transf_to_move->rank++ : $transf_to_move->rank--;
  $transf_to_move->store();
}

$transformation->store();

/** @var CInteropActor $actor */
$actor = new $transformation->actor_class;
$actor->load($transformation->actor_id);

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