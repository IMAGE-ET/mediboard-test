<?php

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Alexis Granger
 */
 
global $can, $m, $AppUI, $dPconfig;

$can->needsAdmin();

/**
 * Packs import
 */
function importPacks($packs){
	global $AppUI, $m, $dPconfig;
	
	// Nombre de packs et d'analyses
	$nb["packs"] = 0;
	$nb["analysesOK"] = 0;
	$nb["analysesKO"] = 0;
	
	
	// Liste des analyses nono trouvees
	$erreurs = array();

	// On crée chaque pack ainsi qu'un id400 associé
	foreach($packs->bilan as $_pack){
		$pack = new CPackExamensLabo();
		$pack->function_id = "";
		$pack->libelle = utf8_decode((string) $_pack->libelle);
		$pack->code = (int) $_pack->code;
		
        // Sauvegarde du pack
        $idPack = new CIdSante400();
        // tag des id externe des packs => nom du laboatoire ==> LABO
        $idPack->tag = "LABO";
        $idPack->id400 = (int) $_pack->code;
      
        $idPack->bindObject($pack);
		
		// On crée les analyses correspondantes
		foreach($_pack->analyses->cana as $_analyse){
		  // Creation de l'analyse
	      $analyse = new CPackItemExamenLabo();
		  
		  // Chargement de l'analyse
		  $examLabo = new CExamenLabo();
		  $whereExam = array();
		  $whereExam['identifiant'] = (string) " = '$_analyse'";
		  $examLabo->loadObject($whereExam);
		
  	      if($examLabo->_id){
		    $analyse->pack_examens_labo_id = $pack->_id;
		    $analyse->examen_labo_id = $examLabo->examen_labo_id;

		    // Sauvegarde de l'analyse et de son id400
		    $idExamen = new CIdSante400();
		    $idExamen->tag = "LABO";
		    $idExamen->id400 = (string) $_analyse;
		    
		    $idExamen->bindObject($analyse);
		    $nb["analysesOK"]++;
  	      } else {
  	      	$erreurs[][(string) $_pack->libelle] = (string) $_analyse;
  	      	$nb["analysesKO"]++;
  	      }
		}
		$nb["packs"]++;
	}
	
	// Recapitulatif des importations
	$AppUI->stepAjax("Packs Importées: ".$nb["packs"], UI_MSG_OK);	
	$AppUI->stepAjax("Analyses Importées: ".$nb["analysesOK"], UI_MSG_OK);	
    $AppUI->stepAjax("Analyses non importées: ".$nb["analysesKO"], UI_MSG_WARNING);	
	foreach($erreurs as $key=>$erreur){
	  foreach($erreur as $_key=>$_erreur){
	    $AppUI->stepAjax("Analyse non trouvée: ".$_erreur." dans le pack ".utf8_decode($_key), UI_MSG_WARNING);
	  }
	}    
}


// Check import configuration
$config = $dPconfig[$m]["CPackExamensLabo"];

if (null == $remote_url = $config["remote_url"]) {
  $AppUI->stepAjax("Remote URL not configured", UI_MSG_ERROR);
}

if (false === $content = file_get_contents($remote_url)) {
  $AppUI->stepAjax("Couldn't connect to remote url", UI_MSG_ERROR);
}


// Check imported catalogue document
$doc = new CMbXMLDocument;

if (!$doc->loadXML($content)) {
  $AppUI->stepAjax("Document is not well formed", UI_MSG_ERROR);
}

$tmpPath = "tmp/dPlabo/import_packs.xml";
CMbPath::forceDir(dirname($tmpPath));
$doc->save($tmpPath);
$doc->load($tmpPath);

if (!$doc->schemaValidate("modules/$m/remote/packs.xsd")) {
  $AppUI->stepAjax("Document is not valid", UI_MSG_ERROR);
}

$AppUI->stepAjax("Document is valid", UI_MSG_OK);

// Check access to idSante400
$canSante400 = CModule::getCanDo("dPsante400");
if (!$canSante400->edit) {
  $AppUI->stepAjax("No permission for module 'dPsante400' or module not installed", UI_MSG_ERROR);
}

// Import packs
$packs = new SimpleXMLElement($content);
try {
  importPacks($packs);
} 
catch (Exception $e) {
  mbTrace($e);
  $AppUI->stepAjax("Couldn't import catalogue for the  reason stated above", UI_MSG_ERROR);
}


?>