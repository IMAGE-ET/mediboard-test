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

ini_set("memory_limit", "1G");

$loops = mbGetValueFromGet("loops", 100);
$trunk = mbGetValueFromGet("trunk", 100);

mbTrace($loops, "loops");
mbTrace($trunk, "trunk");

$problems = array();
for ($loop = 0; $loop < $loops; $loop++) {
  $starting = $loop*$trunk;
  
  $docs = $doc->loadList($where, "compte_rendu_id DESC", "$starting, $trunk");
  foreach ($docs as $_doc) {
	  $source = utf8_encode("<div>$_doc->source</div>");
	  
	  $source = preg_replace("/&\w+;/i", "", $source);
	//  $source = strtr($source, $MSO_replacements);
	//  mbTrace($source);
	//  $source = preg_replace("/\w+:(\w+)/i", "$1", $source);
	//  mbTrace($source);
	  
	  if (false == $validation = @DOMDocument::loadXML($source, LIBXML_NSCLEAN)) {
	//    DOMDocument::loadXML($source, LIBXML_NSCLEAN);
	    $problems[$_doc->_id] = $_doc;
	  }
	}  
}

mbTrace(count($problems), "Problems count");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("problems", $problems);

$smarty->display("check_document.tpl");


?>
