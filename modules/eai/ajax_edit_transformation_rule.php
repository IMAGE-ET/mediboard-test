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

$transformation_ruleset_id = CValue::getOrSession("transformation_ruleset_id");
$transformation_rule_id    = CValue::getOrSession("transformation_rule_id");
$mode_duplication          = CValue::getOrSession("mode_duplication", false);

$transf_rule = new CEAITransformationRule();
$transf_rule->load($transformation_rule_id);

if (!$transf_rule->_id) {
  $transf_rule->eai_transformation_ruleset_id = $transformation_ruleset_id;
}

$standards = CInteropNorm::getObjects();

$transf_ruleset  = new CEAITransformationRuleSet();
$transf_rulesets = $transf_ruleset->loadList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("transf_rule"              , $transf_rule);
$smarty->assign("mode_duplication"         , $mode_duplication);
$smarty->assign("transf_rulesets"          , $transf_rulesets);
$smarty->assign("transformation_ruleset_id", $transformation_ruleset_id);
$smarty->assign("standards"                , $standards);

$smarty->display("inc_edit_transformation_rule.tpl");