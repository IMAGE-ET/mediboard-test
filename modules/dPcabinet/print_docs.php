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

$header = $footer = null;

$consult->loadRefsDocs();
$docs_ids = array_keys($consult->_ref_documents);
foreach($nbDoc as $compte_rendu_id => $nb_print){
  if($nb_print > 0 && in_array($compte_rendu_id, $docs_ids)){
    for($i = 1; $i <= $nb_print; $i++){
      $doc = $consult->_ref_documents[$compte_rendu_id];
      
      // Suppression des headers et footers en trop (tous sauf le premier)
      if (!$header || !$footer) {
        $xml = new DOMDocument;
        $xml->loadHTML($doc->source);
        if ($header = $xml->getElementById("header")) {
          $header->parentNode->removeChild($header);
        }
        if ($footer = $xml->getElementById("footer")) {
          $footer->parentNode->removeChild($footer);
        }
        $doc->source = $xml->saveHTML();
      }
      
      $documents[] = $doc;
    }
  }
}

$source = "";
foreach($documents as $doc) {
  $source .= $doc->source . '<br style="page-break-after: always;" />';
}

// @fixme: hackish way of making a semi-valid HTML doc
$source = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">', "", $source);
$source = str_replace(array("<html>", "</html>", "<body>", "</body>"), "", $source);

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
