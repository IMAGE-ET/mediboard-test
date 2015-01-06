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

$standard = new $standard_name;

$message  = new $message_name;
mbTrace($message);

$profil        = CValue::get("profil", "PAM");
$message_class = CValue::get("message_class", "CHL7EventADTA01");

$temp       = explode("_", $message_class);

$event_name = CMbArray::get($temp, 0);
$version    = CAppUI::conf("hl7 default_version");
$extension = null;
if (CMbArray::get($temp, 1)) {
  $extension    = CAppUI::conf("hl7 default_fr_version");
}

$message = str_replace("CHL7Event", "", $event_name);


if ($extension) {
  $where["extension"] = " = '$extension'";
}

$actor = new CReceiverHL7v2();
$actor->load(1);
$actor->_ref_hl7_transformations = array();

$trans = new CHL7v2Transformation($version, $extension, $message);
$tree = $trans->getSegments($actor);

$smarty = new CSmartyDP("modules/hl7");
$smarty->assign("profil"    , $profil);
$smarty->assign("version"   , $version);
$smarty->assign("extension" , $extension);
$smarty->assign("message"   , $message);
$smarty->assign("tree"      , $tree);
$smarty->assign("actor"      , $actor);
$smarty->assign("actor_guid"      , $actor->_guid);

$smarty->display("inc_transformation_hl7.tpl");