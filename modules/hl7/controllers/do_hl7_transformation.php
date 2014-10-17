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

$actor_guid = CValue::post("actor_guid");
$domain_id  = CValue::post("domain_id");
$profil     = CValue::post("profil");
$version    = CValue::post("version");
$extension  = CValue::post("extension");
$message    = CValue::post("message");
$action     = CValue::post("action");
$components = CValue::post("components");

list($actor_class, $actor_id) = explode('-', $actor_guid);

$components = explode("|", $components);

foreach ($components as $_component) {
  $hl7_transformation              = new CHL7Transformation();
  $hl7_transformation->actor_class = $actor_class;
  $hl7_transformation->actor_id    = $actor_id;
  $hl7_transformation->profil      = $profil;
  $hl7_transformation->version     = $version;
  $hl7_transformation->extension   = $extension;
  $hl7_transformation->message     = $message;
  $hl7_transformation->component   = $_component;
  $hl7_transformation->action      = $action;

  $hl7_transformation->loadMatchingObject();

  $type = $hl7_transformation->_id ? "modify" : "create";

  if ($msg = $hl7_transformation->store()) {
    CAppUI::stepAjax($msg, UI_MSG_WARNING);
  }
  else {
    CAppUI::stepAjax("CHL7Transformation-msg-$type");
  }
}

CApp::rip();