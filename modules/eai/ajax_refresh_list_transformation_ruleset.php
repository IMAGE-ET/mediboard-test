<?php
/**
 * View transformations EAI
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$transformation_ruleset_id = CValue::get("transformation_ruleset_id");

$transf_ruleset  = new CEAITransformationRuleSet();
/** @var CEAITransformationRuleSet[] $transf_rulesets */
$transf_rulesets = $transf_ruleset->loadList();
foreach ($transf_rulesets as $_transf_ruleset) {
  $only_active  = $_transf_ruleset->countRefsEAITransformationRules(true);  //only actives
  $all_elements = $_transf_ruleset->countRefsEAITransformationRules(false); //all elements
  $_transf_ruleset->_count_inactive_transformation_rules = $all_elements - $only_active;
  $_transf_ruleset->_count_active_transformation_rules   = $only_active;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("transf_rulesets", $transf_rulesets);
$smarty->display("inc_list_transformation_ruleset.tpl");
