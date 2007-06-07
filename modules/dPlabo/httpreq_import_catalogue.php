<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Thomas Despoix
 */
 
global $can, $m, $AppUI, $dPconfig, $remote_name;

$can->needsAdmin();


/**
 * Recursive catalogue import
 */
function importCatalogue($cat, $parent_id = null) {  
  global $AppUI, $remote_name;
  
  // Bind a catalogue
  $catalogue = new CCatalogueLabo;
  $catalogue->identifiant = (string) $cat->identifiant;
  $catalogue->libelle = (string) $cat->libelle;
  $catalogue->pere_id = $parent_id;

  // Bind the id400
  $catAtt = $cat->attributes();
  $idCat = new CIdSante400;
  $idCat->tag = $remote_name;
  $idCat->id400 = (string) $catAtt["id"];
  
  $idCat->bindObject($catalogue);

  $AppUI->stepAjax("Catalogue '$catalogue->_view' importé", UI_MSG_OK);

  // Import child catalogues
  foreach ($cat->sections->catalogue as $_catalogue) {
    importCatalogue($_catalogue ,$catalogue->_id);
  }
  
  // Import analyses
  foreach ($cat->analyses->analyse as $_analyse) {
    $analyse = new CExamenLabo;
    $analyse->identifiant = (string) $_analyse->identifiant;
    $analyse->libelle     = (string) $_analyse->libelle;
    $analyse->catalogue_labo_id = $catalogue->_id;
    
    // Type d'analyse
    $type = $_analyse->type;
    if ($numerique = $type->numerique) {
      $analyse->type  = "num";
      $analyse->unite = (string) $numerique->unite;
      $analyse->min   = (string) $numerique->min;
      $analyse->max   = (string) $numerique->min;
    }
    
    if ($texteLibre = $type->texteLibre) {
      $analyse->type = "str";
    }
    
    if ($textLibre = $type->ouiNon) {
      $analyse->type = "bool";
    }

    if ($application = $_analyse->application) {
      $analyse->deb_application = (string) $application->debut;
      $analyse->fin_application = (string) $application->fin;
    }
    
    if ($applicabilite = $_analyse->applicabilite) {
      $analyse->applicabilite = (string) $applicabilite->sexe;
      $analyse->age_min       = (string) $applicabilite->ageMinimum;
      $analyse->age_max       = (string) $applicabilite->ageMaximum;
    }

    if ($prelevement = $_analyse->prelevement) {
      $analyse->type_prelevement     = (string) $prelevement->type;
      $analyse->methode_prelevement  = (string) $prelevement->methode;
      $analyse->quantite_prelevement = (string) $prelevement->quantite;
      $analyse->unite_prelevement    = (string) $prelevement->unite;
    }

    if ($conservation = $_analyse->conservation) {
      $analyse->temps_conservation = mbTranformTime(null, mbDateTimeFromXMLDuration((string) $conservation->duree), "%H");
      $analyse->conservation = (string) $conservation->methode;
    }
    
    if ($execution = $_analyse->execution) {
      $analyse->duree_execution = mbTranformTime(null, mbDateTimeFromXMLDuration((string) $execution->duree), "%H");
      if ($joursSemaine = $execution->joursSemaine) {
        $analyse->execution_lun = "true" == $joursSemaine->lundi    ? "1": "0";
        $analyse->execution_mar = "true" == $joursSemaine->mardi    ? "1": "0";
        $analyse->execution_mer = "true" == $joursSemaine->mercredi ? "1": "0";
        $analyse->execution_jeu = "true" == $joursSemaine->jeudi    ? "1": "0";
        $analyse->execution_ven = "true" == $joursSemaine->vendredi ? "1": "0";
        $analyse->execution_sam = "true" == $joursSemaine->samedi   ? "1": "0";
        $analyse->execution_dim = "true" == $joursSemaine->dimanche ? "1": "0";
      }
    }

    $analyse->technique = (string) $_analyse->technique;
    $analyse->materiel  = (string) $_analyse->materiel;
    $analyse->remarques = (string) $_analyse->remarques;
    
    // Bind the id400
    $anaAtt = $_analyse->attributes();
    $idAna = new CIdSante400;
    $idAna->tag = $remote_name;
    $idAna->id400 = (string) $anaAtt["id"];
    
    $idAna->bindObject($analyse);
  
    $AppUI->stepAjax("Analyse '$analyse->_view' importée", UI_MSG_OK);
  }
}

$config = $dPconfig[$m]["CCatalogueLabo"];

if (null == $remote_name = $config["remote_name"]) {
  $AppUI->stepAjax("Remote name not configured", UI_MSG_ERROR);
}

if (null == $remote_url = $config["remote_url"]) {
  $AppUI->stepAjax("Remote URL not configured", UI_MSG_ERROR);
}

if (false === $content = file_get_contents($remote_url)) {
  $AppUI->stepAjax("Couldn't connect to remote url", UI_MSG_ERROR);
}

$doc = new CMbXMLDocument;
$doc->loadXML($content);
if (!$doc->schemaValidate("modules/$m/remote/catalogue.xsd")) {
  $AppUI->stepAjax("Document is not valid", UI_MSG_ERROR);
}

$AppUI->stepAjax("Document is valid", UI_MSG_OK);

$cat = new SimpleXMLElement($content);
try {
  importCatalogue($cat);
} 
catch (Exception $e) {
  mbTrace($e);
  $AppUI->stepAjax("Couldn't import catalogue for the  reason stated above", UI_MSG_ERROR);
}



?>
