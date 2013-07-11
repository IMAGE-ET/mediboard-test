<?php /* $Id: view_logs.php 6135 2009-04-21 10:49:02Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: 6135 $
* @author Romain Ollivier
*/

CCanDo::checkEdit();

$hash = CValue::get('hash');

if ($hash == 'clean') {
  unlink(LOG_PATH);
  build_error_log();
}

if ($hash) {
  $doc = new DOMDocument();
  @$doc->loadHTMLFile(LOG_PATH);
  
  $xpath = new DOMXPath($doc);
  $elements = $xpath->query("//div[@title='$hash']");
  
  foreach($elements as $element) {
    $element->parentNode->removeChild($element);
  }

  $content = $doc->saveHTML();
  file_put_contents(LOG_PATH, $content);
}


$log_size = filesize(LOG_PATH);
$log_size_limit = 1024*1024*2;

$offset = -1;
if ($log_size > $log_size_limit) {
  $offset = $log_size - $log_size_limit;
}
$log_content = file_get_contents(LOG_PATH, false, null, $offset);

echo $log_content;