<?php /* $Id: addedit_modeles.php 5679 2009-02-18 15:32:10Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 5679 $
* @author Romain Ollivier
*/

global $can;
$can->needsRead();

$doc = new CCompteRendu;
$where = array(
//  "compte_rendu_id" => "= '47325'"
);
  
$MSO_replacements = array (
  "o:p" => "p",
  "w:st" => "st",
  "st1:personname" => "personname",
  "st1:metricconverter" => "metricconverter",
  "o:smarttagtype" => "smarttagtype"
);
  
foreach ($doc->loadList($where, "compte_rendu_id DESC", 10000) as $_doc) {
  $source = utf8_encode("<div>$_doc->source</div>");
  
  $source = preg_replace("/&\w+;/i", "", $source);
//  $source = strtr($source, $MSO_replacements);
//  mbTrace($source);
//  $source = preg_replace("/\w+:(\w+)/i", "$1", $source);
//  mbTrace($source);
  
  if (false == $validation = @DOMDocument::loadXML($source, LIBXML_NSCLEAN)) {
//    DOMDocument::loadXML($source, LIBXML_NSCLEAN);
    mbTrace($_doc->nom, $_doc->_id);  
  }
}


?>
