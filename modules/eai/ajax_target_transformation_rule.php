<?php
/**
 * View transformation rules EAI
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$standard_name = CValue::get("standard");
$domain_name   = CValue::get("domain");
$profil_name   = CValue::get("profil");
$message_name  = CValue::get("message");
$version       = CValue::get("version");
$target        = CValue::get("target");

$error = null;

// Seulement l'arbre des évènements HL7
if ($message_name && strpos($message_name, "CHL7Event") !== false) {
  $temp = explode("_", $message_name);

  $event_name = CMbArray::get($temp, 0);
  $version    = $version ? $version : CAppUI::conf("hl7 default_version");
  $extension = null;

  if (CMbArray::get($temp, 1)) {
    $extension = CAppUI::conf("hl7 default_fr_version");
  }

  $message = str_replace("CHL7Event", "", $event_name);

  if ($extension) {
    $where["extension"] = " = '$extension'";
  }

  $trans = new CHL7v2Transformation($version, $extension, $message);
  $tree = $trans->getSegments();

  $smarty = new CSmartyDP("modules/hl7");
  $smarty->assign("profil"    , $profil_name);
  $smarty->assign("version"   , $version);
  $smarty->assign("extension" , $extension);
  $smarty->assign("message"   , $message);
  $smarty->assign("tree"      , $tree);

  $smarty->display("inc_transformation_hl7.tpl");
}
else {
  $error = !$message_name ? "CEAITransformationRule-msg-choose message" : "CEAITransformationRule-msg-message not supported";

  $smarty = new CSmartyDP();
  $smarty->assign("error", $error);
  $smarty->display("inc_target_transformation_rule.tpl");
}

CApp::rip();
