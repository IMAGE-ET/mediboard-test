<?php /* $Id: view_logs.php 6135 2009-04-21 10:49:02Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: 6135 $
* @author Romain Ollivier
*/

global $can, $logPath;

$can->needsRead();

$hash = mbGetValueFromGet('hash');

if ($hash) {
  $doc = new DOMDocument();
  @$doc->loadHTMLFile($logPath);
  
  $xpath = new DOMXPath($doc);
  $elements = $xpath->query("//div[@title='$hash']");
  
  foreach($elements as $element) {
    $element->parentNode->removeChild($element);
  }
  $content = $doc->saveHTML();

}

else {
  $content = "<h2>Log de Mediboard ré-initialisé depuis ".date("Y-m-d H:i:s")."</h2>";
}

file_put_contents($logPath, $content);
echo $content;