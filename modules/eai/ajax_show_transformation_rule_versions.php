<?php
/**
 * View versions for transformation rule
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$transformation_rule_id = CValue::get("transformation_rule_id");
$standard_name          = CValue::get("standard_name");
$profil_name            = CValue::get("profil_name");

if (!$standard_name) {
  return;
}

$standard = new $standard_name;
$versions_standard = $standard->getVersions();

$versions_profil = array();
if ($profil_name && $profil_name != "none") {
  $profil_name = str_replace("_", "", $profil_name);

  $classname= "C$profil_name";

  $profil = new $classname;
  $versions_profil = $profil->getVersions();
}

$versions = $versions_profil ? $versions_profil : $versions_standard;

$transformation_rule = new CEAITransformationRule();
$transformation_rule->load($transformation_rule_id);

$smarty = new CSmartyDP();
$smarty->assign("versions"           , $versions);
$smarty->assign("transformation_rule", $transformation_rule);
$smarty->display("inc_select_enum_versions.tpl");
