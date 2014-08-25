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


$object_class     = trim(CValue::getOrSession("object_class"));
$object_id        = trim(CValue::getOrSession("object_id"));
$cn_receiver_guid = trim(CValue::getOrSessionAbs("cn_receiver_guid"));

$object = null;

if ($object_class && $object_id) {
  $object = CMbObject::loadFromGuid("$object_class-$object_id");
}

$receiver           = new CReceiverHL7v2();
$receiver->group_id = CGroups::loadCurrent()->_id;
$receiver->actif    = "1";
$receivers          = $receiver->loadMatchingList();

$object_classes = array("COperation", "CSejour");

$smarty = new CSmartyDP();
$smarty->assign("object_class"    , $object_class);
$smarty->assign("object_classes"  , $object_classes);
$smarty->assign("object_id"       , $object_id);
$smarty->assign("object"          , $object);
$smarty->assign("receivers"       , $receivers);
$smarty->assign("cn_receiver_guid", $cn_receiver_guid);
$smarty->display("vw_test_hl7v2.tpl");