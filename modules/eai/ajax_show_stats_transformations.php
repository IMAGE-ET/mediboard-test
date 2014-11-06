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
$transformations = $transf_rule->loadRefsEAITransformation();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("transf_rule"    , $transf_rule);
$smarty->assign("transformations", $transformations);
$smarty->display("inc_show_stats_transformations.tpl");