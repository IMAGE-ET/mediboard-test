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
$docs_ids = array_keys($consult->_ref_documents);
foreach($nbDoc as $compte_rendu_id => $nb_print){
  if($nb_print > 0 && in_array($compte_rendu_id, $docs_ids)){
    for($i = 1; $i <= $nb_print; $i++){
      $doc = $consult->_ref_documents[$compte_rendu_id];
      
      // Suppression des headers et footers en trop (tous sauf le premier)
      $xml = new DOMDocument;
      $source = utf8_encode("<div>$doc->source</div>");
      $source = preg_replace("/&\w+;/i", "", $source);
      
      @$xml->loadXML($source);
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
      
      $doc->source = $xml->saveHTML();
      
      $documents[] = $doc;
    }
  }
}

$source = "";
foreach($documents as $key => $doc) {
  $source .= $doc->source . '<br style="page-break-after: always;" />';
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
