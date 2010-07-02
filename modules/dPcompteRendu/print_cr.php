<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// Récupération du compte-rendu
$compte_rendu_id = CValue::get("compte_rendu_id", 0);

$compte_rendu = new CCompteRendu;
$compte_rendu->load($compte_rendu_id);

// Utilisation des headers/footers
if ($compte_rendu->header_id || $compte_rendu->footer_id) {
  $compte_rendu->loadComponents();
  
  $header = $compte_rendu->_ref_header;
  $footer = $compte_rendu->_ref_footer;
  
  $header->height = isset($header->height) ? $header->height : 20;
  $footer->height = isset($footer->height) ? $footer->height : 20;
  
  $style = "
<style type=\"text/css\">
  #header {
    height: {$header->height}px;
  }

  #footer {
    height: {$footer->height}px;
  }";
  
  if ($header->_id) {
    $header->_source->content = "<div id=\"header\">$header->_source->content</div>";
    $header->height += 20;
    $compte_rendu->header_id = null;
  }
  
  if ($footer->_id) {
    $footer->_source->content = "<div id=\"footer\">$footer->_source->content</div>";
    $footer->height += 20;
    $compte_rendu->footer_id = null;
  }
  
  $style.= "
  @media print {
    #body { 
      padding-top: {$header->height}px; 
    }

    hr.pagebreak, hr.pageBreak { 
      padding-top: {$header->height}px; 
    }
  }
</style>";

  $compte_rendu->_source = "<div id=\"body\">$compte_rendu->_source</div>";
  $compte_rendu->_source = $style . $header->_source . $footer->_source . $compte_rendu->_source;
}

// Initialisation de FCKEditor
$templateManager = new CTemplateManager;
$templateManager->printMode = true;
$templateManager->initHTMLArea();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("_source", $compte_rendu->_source);
$smarty->display("print_cr.tpl");
