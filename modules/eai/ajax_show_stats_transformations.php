<?php
/**
 * View stats transformations for transformation rule
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$transformation_rule_id = CValue::getOrSession("transformation_rule_id");

$transf_rule = new CEAITransformationRule();
$transf_rule->load($transformation_rule_id);

foreach ($transf_rule->loadRefsEAITransformation("actor_class ASC, actor_id ASC") as $_transformation) {
  $_transformation->loadRefActor();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("transf_rule", $transf_rule);
$smarty->display("inc_show_stats_transformations.tpl");