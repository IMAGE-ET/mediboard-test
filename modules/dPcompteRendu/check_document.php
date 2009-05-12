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
set_time_limit(300);

$loops = mbGetValueFromGet("loops", 100);
$trunk = mbGetValueFromGet("trunk", 100);

mbTrace($loops, "loops");
mbTrace($trunk, "trunk");

//$problems = array();
//for ($loop = 0; $loop < $loops; $loop++) {
//  $starting = $loop*$trunk;
//  CSQLDataSource::$trace= true;
//  $docs = $doc->loadList($where, "compte_rendu_id DESC", "$starting, $trunk");
//  CSQLDataSource::$trace= false;
//  foreach ($docs as $_doc) {
//	  $source = utf8_encode("<div>$_doc->source</div>");
//	  
//	  $source = preg_replace("/&\w+;/i", "", $source);
//	  
//	  if (false == $validation = @DOMDocument::loadXML($source)) {
//	    $problems[$_doc->_id] = $_doc;
//	  }
//	}  
//}



for ($loop = 0; $loop < $loops; $loop++) {
  $starting = $loop*$trunk;
  $ds = $doc->_spec->ds;
  
	$query = "SELECT `compte_rendu`.`compte_rendu_id`, `compte_rendu`.`source` 
		FROM compte_rendu 
		ORDER BY compte_rendu_id DESC
		LIMIT $starting, $trunk";
  $docs = $ds->loadHashList($query);
  foreach ($docs as $doc_id => $doc_source) {
	  $source = utf8_encode("<div>$doc_source</div>");
	  $source = preg_replace("/&\w+;/i", "", $source);
	  
	  if (false == $validation = @DOMDocument::loadXML($source)) {
	    $doc = new CCompteRendu;
	    $doc->load($doc_id);
	    $problems[$doc_id] = $doc;
	  }
	}  
}

mbTrace(count($problems), "Problems count");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("problems", $problems);

$smarty->display("check_document.tpl");


?>
