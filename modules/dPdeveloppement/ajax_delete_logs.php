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

if ($hash == 'clean') {
  unlink(CError::LOG_PATH);
  build_error_log();
}

if ($hash) {
  $doc = new DOMDocument();
  @$doc->loadHTMLFile(CError::LOG_PATH);
  
  $xpath = new DOMXPath($doc);
  $elements = $xpath->query("//div[@title='$hash']");
  
  foreach ($elements as $element) {
    $element->parentNode->removeChild($element);
  }

  $content = $doc->saveHTML();
  file_put_contents(CError::LOG_PATH, $content);
}

$log_size = filesize(CError::LOG_PATH);
$log_size_limit = CError::LOG_SIZE_LIMIT;

$offset = -1;
if ($log_size > $log_size_limit) {
  $offset = $log_size - $log_size_limit;
}
$log_content = file_get_contents(CError::LOG_PATH, false, null, $offset);

$log_size_deca = CMbString::toDecaBinary($log_size);
CAppUI::js("Control.Tabs.setTabCount('error-file', '$log_size_deca')");

echo $log_content;