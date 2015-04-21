<?php

/**
 * $Id$
 *
 * @category Developpement
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 18997 $
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$hash = CValue::get('hash');
$type = CValue::get("type", "logs-file");

$filename = null;
switch ($type) {
  case "debug-file":
    $filename          = CMbDebug::DEBUG_PATH;
    $control_tabs_name = "debug";
    break;

  default:
    $filename = CError::LOG_PATH;
    $control_tabs_name = "error-file";
}

if ($hash == 'clean') {
  unlink($filename);
  build_error_log();
}

if ($hash) {
  $doc = new DOMDocument();
  @$doc->loadHTMLFile($filename);
  
  $xpath = new DOMXPath($doc);
  $elements = $xpath->query("//div[@title='$hash']");
  
  foreach ($elements as $element) {
    $element->parentNode->removeChild($element);
  }

  $content = $doc->saveHTML();
  file_put_contents(CError::LOG_PATH, $content);
}

$log_content = null;
$log_size    = 0;
if (file_exists($filename)) {
  $log_size = filesize($filename);
  $log_size_limit = CError::LOG_SIZE_LIMIT;

  $offset = -1;
  if ($log_size > $log_size_limit) {
    $offset = $log_size - $log_size_limit;
  }
  $log_content = file_get_contents($filename, false, null, $offset);
}

$log_size_deca = CMbString::toDecaBinary($log_size);
if (CAppUI::conf("error_logs_in_db")) {
  CAppUI::js("Control.Tabs.setTabCount('$control_tabs_name', '$log_size_deca')");
}

echo $log_content;