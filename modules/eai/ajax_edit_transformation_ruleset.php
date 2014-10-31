<?php
/**
 * View interop actors EAI
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

$transf_ruleset = new CEAITransformationRuleSet();
$transf_ruleset->load($transformation_ruleset_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("transf_ruleset", $transf_ruleset);
$smarty->display("inc_edit_transformation_ruleset.tpl");