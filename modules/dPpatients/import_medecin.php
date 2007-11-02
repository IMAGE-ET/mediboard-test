<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m;

//set_time_limit(300);

if (!class_exists("DOMDocument")) {
  trigger_error("sorry, DOMDocument is needed");
  return;
}

class CMbXPath extends DOMXPath {
  function __construct(DOMDocument $doc) {
    parent::__construct($doc);
  }

  function queryUniqueNode($query, DOMNode $contextNode) {
    $nodeList = parent::query($query, $contextNode);
    if ($nodeList->length > 1) {
    	logParseError("queried node is not unique, found $nodeList->length occurence(s) for '$query'");
      return null;
    }
    
    return $nodeList->item(0);
  } 
  
  function queryTextNode($query, DOMNode $contextNode, $purgeChars = "") {
    $text = null;
    $node = $this->queryUniqueNode($query, $contextNode);
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $text = $node->textContent;
      $text = str_replace(CMbArray::fromString($purgeChars), "", $text);
      $text = trim($text);
    }
    
    return $text;
  } 

  function queryMultilineTextNode($query, DOMNode $contextNode, $purgeChars = "") {
    $text = null;
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $textLines = explode("\n", utf8_decode($node->textContent));
      if (count($textLines) > 1) {
      }
      foreach ($textLines as &$textLine) {
        $textLine = str_replace(CMbArray::fromString($purgeChars), "", $textLine);
        $textLine = trim($textLine);
      }
      
      $text = implode($textLines, "\n");
    } 
    
    return $text;
  }
}

// Make data XML compliant
function purgeHTML($str) {
  // Remove some HTML Entities (compatibility with XML Tree)
  $str = str_replace(
    array('&eacute;', '&nbsp;', '&ecirc;'),
    array('é', ' ', 'ê'),
    $str);
    
  // Remove doctype 
  $str = preg_replace("/<!DOCTYPE[^>]*>/i", "", $str);
  
  // Turn non-entity & to &amp;
  $str = preg_replace("/&(\w*)(?![;\w])/i", "&amp;$1", $str);
  
  // Enquote all attributes
  $str = preg_replace("/ ([^=]+)=([^ |^>|^'|^\"]+)/i", " $1='$2'", $str);
  
  // Self-close HTML empty elements
  $str = preg_replace("/<(img|area|input|br|link|meta)([^>]*)>/i", "<$1$2 />", $str);
  
  // Remove extra-closures
  $str = preg_replace("/<\/tr>([^<>]*)<\/tr>/i", "</tr>$1", $str);
  
  // Remove all attributes
  $str = preg_replace("/ [\w-]*='[^']*'/i", "", $str);
  $str = preg_replace("/ [\w-]*=\"[^\"]*\"/i", "", $str);

  // Turn <br /> into \n. Mendatory for nodeValue properties
  $str = str_replace("<br />", "\n", $str);
  
  
  
  return $str;
}

$parse_errors = 0;

function logParseError($str) {
  global $parse_errors;
  $parse_errors++;
  trigger_error($str, E_USER_WARNING);
}

// Chrono start
$chrono = new Chronometer;
$chrono->start();

$path = $AppUI->getTmpPath("medecin.htm");
$segment = 1000;
$step = @$_GET["step"];
$from = $step ? 100 + $segment * ($step -1) : 0;
$to = $step ? 100 + $step * $segment : 100;

if (mbGetValueFromGet("curl", 1)) {
  
  // Emulates an HTTP request
  $cookiepath = $AppUI->getTmpPath("cookie.txt");
  $baseurl = "http://www.conseil-national.medecin.fr/";
  $fileurl = $step ? "index.php?url=annuaire/result.php&from=$from&to=$to" : "annuaire.php?cp=";
  $url = $baseurl . $fileurl;
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
  curl_setopt($ch, CURLOPT_VERBOSE, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec ($ch);
  curl_close($ch);
  
  $path = $AppUI->getTmpPath("medecin.htm");
  file_put_contents($path, $result);
  
  // -- Step: Get data from html file
  $str = @file_get_contents($path);
  if (!$str) {
    // Création du template
    $smarty = new CSmartyDP();
    
    $smarty->assign("end_of_process", true);
    $smarty->display("import_medecin.tpl");
    return;
  }
} else {
  $str = file_get_contents($path);
}
 
$str = purgeHTML($str); 

// Step: Save data on another file
$purged = $AppUI->getTmpPath("medecin_purged.htm");
$bytes = file_put_contents($purged, $str);

// Step: Parse XML Tree
$doc = new DOMDocument();
$doc->loadHTML($str);

$query = "/html/body/table/tr[3]/td/table/tr/td[2]/table/tr[7]/td/table/*";
$xpath = new CMbXPath($doc);
foreach ($xpath->query($query) as $key => $nodeMainTr) {
  if ($nodeMainTr->nodeName != "tr") {
    logParseError("Not a main <tr>");
    continue;
  }
    
  $ndx = intval($key / 3);
  $mod = intval($key % 3);
    
  if (!isset($medecins[$ndx])) {
    $medecins[$ndx] = new CMedecin;
  }
  
  $xpath2 = new CMbXPath($doc);
 
  $medecin =& $medecins[$ndx];
  switch ($mod) {
    case 0:
    // Nom du médecin
    $query = "td[2]/table/tr[1]/td/b";
    $nom = $xpath2->queryTextNode($query, $nodeMainTr);
    $nom = substr($nom, 0, -5); 
    $fragments = explode(" ", $nom, 2);
    $medecin->prenom = @$fragments[0];
    $medecin->nom    = @$fragments[1];

    // Nom de jeune fille
    $query = "td[2]/table/tr[2]/td";
    $medecin->jeunefille = $xpath2->queryTextNode($query, $nodeMainTr);
            
    break;
      
    case 1:
    // Disciplines qualifiantes
    $query = "td[1]/table/tr[2]/td";
    $medecin->disciplines = $xpath2->queryMultilineTextNode($query, $nodeMainTr, "-");

    // Mentions et orientations
    $query = "td[1]/table/tr[4]/td";
    $medecin->orientations = $xpath2->queryMultilineTextNode($query, $nodeMainTr, "-");

    // Disciplines complémentaires
    $query = "td[2]/table/tr[2]/td";
    $medecin->complementaires = $xpath2->queryMultilineTextNode($query, $nodeMainTr, "-");
    
    break;
    
    case 2:
    // Adresse 
    $query = "td[1]/table/tr[2]/td";
    $node = $xpath2->queryUniqueNode($query, $nodeMainTr);
    $medecin->adresse = $xpath2->queryMultilineTextNode($query, $nodeMainTr);
    
    // Ville
    $query = "td[1]/table/tr[3]/td";
    $ville = $xpath2->queryMultilineTextNode($query, $nodeMainTr);
    $medecin->ville = substr($ville, 6);
    $medecin->cp    = substr($ville, 0, 5);

    // Contact
    $query = "td[2]/table/tr[1]/td[3]";
    $medecin->tel = $xpath2->queryTextNode($query, $nodeMainTr, " /-.");

    $query = "td[2]/table/tr[2]/td[3]";
    $medecin->fax = $xpath2->queryTextNode($query, $nodeMainTr, " /-.");

    $query = "td[2]/table/tr[3]/td[3]";
    $medecin->email = $xpath2->queryTextNode($query, $nodeMainTr);

    break;
  }
}

$stores = 0;
$sibling_errors = 0;

foreach ($medecins as &$medecin) {
  $medecin->_has_siblings = count($medecin->getExactSiblings());
  if ($medecin->_has_siblings) {
    $sibling_errors++;
    continue;
  } 
  
  if (null != $msg = $medecin->store()) {
    $stores++;
    mbTrace($msg);
  }
  
  $medecin->updateFormFields();  
}

$chrono->stop();

// Création du template
$smarty = new CSmartyDP();

$smarty->debugging = false;
$smarty->assign("long_display", false);

$smarty->assign("end_of_process", false);
$smarty->assign("step"          , $step);
$smarty->assign("from"          , $from);
$smarty->assign("to"            , $to);
$smarty->assign("medecins"      , $medecins);
$smarty->assign("chrono"        , $chrono);
$smarty->assign("parse_errors"  , $parse_errors);
$smarty->assign("stores"        , $stores);
$smarty->assign("sibling_errors", $sibling_errors);

$smarty->display("import_medecin.tpl");
?>
