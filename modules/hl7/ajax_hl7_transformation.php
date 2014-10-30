<?php

/**
 * $Id$
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$actor_guid    = CValue::get("actor_guid");
$profil        = CValue::get("profil");
$message_class = CValue::get("message_class");

$temp       = explode("_", $message_class);

$event_name = CMbArray::get($temp, 0);
$version    = CAppUI::conf("hl7 default_version");
$extension = null;
if (CMbArray::get($temp, 1)) {
  $extension    = CAppUI::conf("hl7 default_fr_version");
}

$message = str_replace("CHL7Event", "", $event_name);

/** @var CInteropActor $actor */
$actor = CMbObject::loadFromGuid($actor_guid);
$where = array(
  "message"     => " = '$message'",
  "profil"      => " = '$profil'"
);

if ($extension) {
  $where["extension"] = " = '$extension'";
}

$trans = new CHL7v2Transformation($version, $extension, $message);
$tree = $trans->getSegments($actor);

$smarty = new CSmartyDP();
$smarty->assign("profil"    , $profil);
$smarty->assign("version"   , $version);
$smarty->assign("extension" , $extension);
$smarty->assign("message"   , $message);
$smarty->assign("tree"      , $tree);
$smarty->assign("actor_guid", $actor_guid);
$smarty->assign("actor"     , $actor);

$smarty->display("inc_transformation_hl7.tpl");