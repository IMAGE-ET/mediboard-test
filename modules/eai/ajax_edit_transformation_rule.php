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

$transf_rule = new CEAITransformationRule();
$transf_rule->load($transformation_rule_id);

if (!$transf_rule->_id) {
  $transf_rule->eai_transformation_ruleset_id = $transformation_ruleset_id;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("transf_rule", $transf_rule);
$smarty->display("inc_edit_transformation_rule.tpl");