<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$er7   = CValue::post("er7");
$query = CValue::post("query");

$hl7_message = new CHL7v2Message;
$hl7_message->parse($er7);

$xml = $hl7_message->toXML();

if ($query) {
  CAppUI::requireLibraryFile("geshi/geshi");
  
  $xpath = new CMbXPath($xml);
  $results = @$xpath->query("//$query");
  
  $nodes = array();
  
  // Création du template
  $smarty = new CSmartyDP();

  if ($results) {
    foreach($results as $result) {
      $geshi = new Geshi($xml->saveXML($result), "xml");
      $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
      $geshi->set_overall_style("max-height: 100%; white-space:pre-wrap;");
      $geshi->enable_classes();
      
      $nodes[] = $geshi->parse_code();
    }
  }
  
  $smarty->assign("nodes", $nodes);
  $smarty->display("inc_er7_xml_result.tpl");
}
else {
  ob_clean();
  
  header("Content-Type: text/xml");
  echo $xml->saveXML();
  
  CApp::rip();
}
