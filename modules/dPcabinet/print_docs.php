<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $can;

$consultation_id = CValue::get("consultation_id");
$nbDoc           = CValue::get("nbDoc");
$documents       = array();

// Consultation courante
$consult = new CConsultation();
$consult->load($consultation_id);
$can->edit &= $consult->canEdit();

$can->needsEdit();
$can->needsObject($consult);

$headerFound = $footerFound = false;

$consult->loadRefsDocs();

foreach($nbDoc as $compte_rendu_id => $nb_print){
  if(($nb_print > 0) && isset($consult->_ref_documents[$compte_rendu_id])){
    for($i = 1; $i <= $nb_print; $i++){
      $documents[] = $consult->_ref_documents[$compte_rendu_id];
    }
  }
}

$source = "";
foreach($documents as $doc) {
  // Suppression des headers et footers en trop (tous sauf le premier)
  $xml = new DOMDocument;
  $_source = utf8_encode("<div>$doc->source</div>");
  $_source = preg_replace("/&\w+;/i", "", $_source);
  
  @$xml->loadXML($_source);
  $xpath = new DOMXPath($xml);
  
  $nodeList = $xpath->query("//*[@id='header']");
  if ($nodeList->length) {
    if ($headerFound) {
      $header = $nodeList->item(0);
      $header->parentNode->removeChild($header);
    }
    $headerFound = true;
  }
  
  $nodeList = $xpath->query("//*[@id='footer']");
  if ($nodeList->length) {
    if ($footerFound) {
      $footer = $nodeList->item(0);
      $footer->parentNode->removeChild($footer);
    }
    $footerFound = true;
  }
    
  $source .= $xml->saveHTML() . '<br style="page-break-after: always;" />';
}

// Initialisation de FCKEditor
$templateManager = new CTemplateManager;
$templateManager->printMode = true;
$templateManager->initHTMLArea();

if (count($documents) == 0) {
  echo '<div class="small-info">Il n\'y a aucun document pour cette consultation</div>';
  Capp::rip();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("source", $source);
$smarty->display("../../dPcompteRendu/templates/print_cr.tpl");
