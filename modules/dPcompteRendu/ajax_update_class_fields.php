<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

function searchSpan(&$xml) {
  foreach ($xml->childNodes as $_node) {
    if ($_node->nodeName === "span" && ($_node->getAttribute("class") === "name" || $_node->getAttribute("class") === "field") &&
      $_node->childNodes->length != 1 && $_node->firstChild && ($_node->firstChild->nodeType != XML_TEXT_NODE || !preg_match("/\[.+\]/", $_node->nodeValue))) {
      return true;
    }
    if ($_node->childNodes) {
      searchSpan($_node);
    }
  }
}

$compte_rendu = new CCompteRendu;
$where = array();
$where["object_id"] = "IS NULL";
$compte_rendus = $compte_rendu->loadList($where, null, "350000");

$list = array();

$xml = new DOMDocument('1.0', 'iso-8859-1');
foreach ($compte_rendus as $_compte_rendu) {
  mbTrace($_compte_rendu->_id,'',1);
  $_compte_rendu->loadContent();
  $content = CMbString::convertHTMLToXMLEntities($_compte_rendu->_source);
  $content = utf8_encode(CHtmlToPDF::cleanWord($content));
  $xml->loadXML("<div>".$content."</div>");
  if (searchSpan($xml->documentElement)) {
    $list[] = $_compte_rendu;
  }
}

$smarty = new CSmartyDP;
$smarty->assign("list", $list);
$smarty->display("inc_update_class_fields.tpl");

?>