<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m;

set_time_limit(150);

if (!class_exists("DOMDocument")) {
  trigger_error("sorry, DOMDocument is needed");
  return;
}

if (null == $pass = mbGetValueFromGet("pass")) {
  $AppUI->stepAjax("Fonctionnalité désactivée car trop instable.", UI_MSG_WARNING);
  return;
}

if (md5($pass) != "aa450aff6d0f4974711ff4c5536ed4cb") {
  $AppUI->stepAjax("Mot de passe incorrect.\nAttention, fonctionnalité à utiliser avec une extrême prudence", UI_MSG_ERROR);
}

// Chrono start
$chrono = new Chronometer;
$chrono->start();

$path = $AppUI->getTmpPath("medecin.htm");
$segment = mbGetValueFromGet("segment", 1000);
$step = mbGetValueFromGet("step", 1);
$from = $step > 1 ? 100 + $segment * ($step-2) : 0;
$to = $step > 1 ? 100 + ($step-1) * $segment : 100;

$padded = str_pad($step, "3", "0", STR_PAD_LEFT);
$path = "tmp/ordre/medecin$padded.htm";
CMbPath::forceDir(dirname($path));

if (mbGetValueFromGet("curl", "0")) {
  // Emulates an HTTP request
  $cookiepath = $AppUI->getTmpPath("cookie.txt");
  $baseurl = "http://www.conseil-national.medecin.fr/";
  $fileurl = $step > 1 ? "index.php?url=annuaire/result.php&from=$from&to=$to" : "annuaire.php?cp=";
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
 
// Purge HTML
if (null == $html = file_get_contents($path)) {
  $AppUI->stepAjax("Fichier '$path' non disponible", UI_MSG_ERROR);
}

// Small adjustments for line delimitation:  <br/> to \n
$html = str_replace("<br>", "\n", $html);

// Prepare the document
$doc = @DOMDocument::loadHTML($html);
file_put_contents("$path.xml", $doc->saveXML());

$xpath = new CMbXPath($doc);

$query = "/html/body/table/tr[3]/td/table/tr/td[2]/table/tr[7]/td/table/*";
$medecins = array();
$xpath_screwed = false;
foreach ($xpath->query($query) as $key => $nodeMainTr) {
  $ndx = intval($key / 3);
  $mod = intval($key % 3);
  
  if ($nodeMainTr->nodeName != "tr") {
    trigger_error("Not a main &lt;tr&gt; DOM Node", E_USER_WARNING);
    $xpath_screwed = true;
    break;
  }
  
  // Création du médecin
  if (!array_key_exists($ndx, $medecins)) {
    $medecins[$ndx] = new CMedecin;
  }
 
  $medecin =& $medecins[$ndx];
  
  $xpath2 = new CMbXPath($doc);
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
    $medecin->disciplines = $xpath2->queryMultilineTextNode($query, $nodeMainTr, "- ");

    // Mentions et orientations
    $query = "td[1]/table/tr[4]/td";
    $medecin->orientations = $xpath2->queryMultilineTextNode($query, $nodeMainTr, "- ");

    // Disciplines complémentaires
    $query = "td[2]/table/tr[2]/td";
    $medecin->complementaires = $xpath2->queryMultilineTextNode($query, $nodeMainTr, "- ");
    
    break;
    
    case 2:
    // Adresse 
    $query = "td[1]/table/tr[2]/td";
    $node = $xpath2->queryUniqueNode($query, $nodeMainTr);
    $medecin->adresse = $xpath2->queryMultilineTextNode($query, $nodeMainTr);
    
    // Ville
    $query = "td[1]/table/tr[3]/td";
    $ville = $xpath2->queryMultilineTextNode($query, $nodeMainTr);
    $medecin->ville = trim(substr($ville, 6));
    $medecin->cp    = trim(substr($ville, 0, 5));
    
    // Hack,: le trop de fonctionne pas sur certains cp de la forme 'NN '.
    if (strlen($medecin->cp) < 5 ) {
      $medecin->cp = substr($medecin->cp, 0, -1);
    }

    // Contact
    $query = "td[2]/table/tr[1]/td[3]";
    $medecin->tel = $xpath2->queryNumcharNode($query, $nodeMainTr, 10);

    $query = "td[2]/table/tr[2]/td[3]";
    $medecin->fax = $xpath2->queryNumcharNode($query, $nodeMainTr, 10);

    $query = "td[2]/table/tr[3]/td[3]";
    $medecin->email = $xpath2->queryTextNode($query, $nodeMainTr);

    break;
  } 
}

$errors = 0;
$updates = 0;

foreach ($medecins as &$medecin) {
  // Recherche des siblings
  $siblings = $medecin->loadExactSiblings();
  if ($medecin->_has_siblings = count($siblings)) {
    $sibling = reset($siblings);
    $medecin->_id = $sibling->_id;
    $updates++;
  } 

  // Sauvegarde
  $medecin->repair();
  if ($msg = $medecin->store()) {
    trigger_error("Error storing $medecin->nom $medecin->prenom ($medecin->cp) : $msg", E_USER_WARNING);
    $errors++;
  }
  
  $medecin->updateFormFields();  
}

$chrono->stop();

$AppUI->stepAjax("Etape $step \n$errors erreurs d'enregistrements", $errors ? UI_MSG_OK : UI_MSG_ALERT);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("verbose", mbGetValueFromGet("verbose"));

$smarty->assign("xpath_screwed" , $xpath_screwed);
$smarty->assign("step"          , $step);
$smarty->assign("from"          , $from);
$smarty->assign("to"            , $to);
$smarty->assign("medecins"      , $medecins);
$smarty->assign("chrono"        , $chrono);
$smarty->assign("updates"       , $updates);
$smarty->assign("errors"        , $errors);

$smarty->display("import_medecin.tpl");
?>