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

$eai_transformation_rule_id     = CValue::post("eai_transformation_rule_id");
$transformation_ruleset_dest_id = CValue::post("transformation_ruleset_dest_id");

$trans_rule = new CEAITransformationRule();
$trans_rule->load($eai_transformation_rule_id);
$trans_rule->_id = '';

if ($transformation_ruleset_dest_id == $trans_rule->eai_transformation_ruleset_id) {
  $trans_rule->name .= CAppUI::tr("copy_suffix");
}
$trans_rule->eai_transformation_ruleset_id = $transformation_ruleset_dest_id;

$msg = $trans_rule->store();
CAppUI::displayMsg($msg, "CEAITransformationRule-msg-create");

CAppUI::js(CValue::post("callback")."()");