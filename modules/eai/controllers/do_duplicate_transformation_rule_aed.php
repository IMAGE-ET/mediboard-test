<?php 

/**
 * Duplicate an transformation to another (or the same) category
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$eai_transformation_rule_id     = CValue::post("eai_transformation_rule_id");
$eai_transformation_ruleset_id  = CValue::post("eai_transformation_ruleset_id");
$transformation_ruleset_dest_id = CValue::post("transformation_ruleset_dest_id");

$transf_rule = new CEAITransformationRule();

// On duplique toutes les règles de la catégorie
if ($eai_transformation_ruleset_id) {
  $transf_rule->eai_transformation_ruleset_id = $eai_transformation_ruleset_id;
  /** @var $transf_rules CEAITransformationRule[] */
  $transf_rules = $transf_rule->loadMatchingList();

  foreach ($transf_rules as $_transf_rule) {
    $msg = $_transf_rule->duplicate($transformation_ruleset_dest_id);
    CAppUI::displayMsg($msg, "CEAITransformationRule-msg-create");
  }
}
// On duplique une seule règle
else {
  $transf_rule->load($eai_transformation_rule_id);

  $msg = $transf_rule->duplicate($transformation_ruleset_dest_id);
  CAppUI::displayMsg($msg, "CEAITransformationRule-msg-create");
}

CAppUI::js(CValue::post("callback")."()");