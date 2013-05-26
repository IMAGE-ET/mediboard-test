<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$source_guid       = CValue::post("source_guid");
$current_directory = CValue::post("current_directory");
$files             = CValue::read($_FILES, "import");

$message = array(
  "result" => "Ajout du fichier",
  "resultNumber" => 0,
  "error" => array(),
);

/** @var CSourceFTP $source */
$source = CMbObject::loadFromGuid($source_guid);
foreach ($files["name"] as $index => $_file) {
  if (!$_file) {
    continue;
  }

  try {
    $source->addFile($files["tmp_name"][$index], $_file, $current_directory);
    $message["resultNumber"]++;
  }
  catch(CMbException $e) {
    $message["error"][] = CAppUI::tr("CExhangeFile-error", $_file);
  }
}

CAppUI::callbackAjax("window.parent.ExchangeSource.closeAfterSubmit", $message);

CApp::rip();
