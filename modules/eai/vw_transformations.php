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

$transf_ruleset  = new CEAITransformationRuleSet();
/** @var CEAITransformationRuleSet[] $transf_rulesets */
$transf_rulesets = $transf_ruleset->loadList();
foreach ($transf_rulesets as $_transf_ruleset) {
  $_transf_ruleset->countRefsEAITransformationRules();
  $_transf_ruleset->countRefsEAITransformationRulesOnlyActive();
  $_transf_ruleset->countRefsEAITransformationRulesOnlyInactive();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("transf_rulesets", $transf_rulesets);
$smarty->display("vw_transformations.tpl");
