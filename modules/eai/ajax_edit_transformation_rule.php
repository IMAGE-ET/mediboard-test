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

$standards_flat = array();
foreach ($standards as $_standard_name => $_standards) {
  foreach ($_standards as $_domain_name => $_domains) {
    foreach ($_domains as $_profil_name => $_profils) {
      foreach ($_profils as $_transaction_name => $_transactions) {
        foreach ($_transactions as $_event_name => $_event) {
          $standards_flat[]  = array(
            "standard"      => $_standard_name,
            "domain"        => $_domain_name,
            "profil"        => $_profil_name,
            "transaction"   => $_transaction_name,
            "message"       => $_event,
          );
        }
      }
    }
  }
}

$transf_ruleset  = new CEAITransformationRuleSet();
$transf_rulesets = $transf_ruleset->loadList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("transf_rule"              , $transf_rule);
$smarty->assign("mode_duplication"         , $mode_duplication);
$smarty->assign("transf_rulesets"          , $transf_rulesets);
$smarty->assign("transformation_ruleset_id", $transformation_ruleset_id);
$smarty->assign("standards"                , $standards);
$smarty->assign("standards_flat"           , $standards_flat);

$smarty->display("inc_edit_transformation_rule.tpl");