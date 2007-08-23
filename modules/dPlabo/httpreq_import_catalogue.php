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
 * Catalogue import
 */
function importCatalogue($cat, $parent_id = null) {  
  global $AppUI, $remote_name;
  
  set_time_limit(180);
  
  $catalogues = array();
  // Creation du catalogue global LABO
  $catal = new CCatalogueLabo();
  
  $catal->identifiant = substr(hash('md5',$remote_name), 0, 4);  // libelle modifié par hash
  
  $catal->libelle = $remote_name;
  $catal->pere_id = $parent_id;
  
  // creation de son id400
  $idCat = new CIdSante400();
  $idCat->tag = $remote_name;
  $idCat->id400 = $remote_name;
  
  $idCat->bindObject($catal);
  $AppUI->stepAjax("Catalogue '$catal->libelle' importé", UI_MSG_OK);
  
  $path = $remote_name;
  // on met a jour $catalogues
  $catalogues[$path] = $catal;
          
  //Parcours des analyses
  foreach($cat->analyse as $_analyse){ 
    $chapitre = (string) $_analyse->chapitre;
  	$path = "$remote_name/$chapitre/";
    if(!$chapitre){
    	$path = $remote_name;
    }
  	//$pathssChap = "$remote_name/$analyse_->sschapitre";
    $catalogue = new CCatalogueLabo();
    
    // si le catalogue n'existe pas deja
    if(!array_key_exists($path,$catalogues)){
      // creation du catalogue
      $catalogue->identifiant = substr(hash('md5',$chapitre), 0, 4);  // libelle modifié par hash;
      $catalogue->libelle = $chapitre;
      
      $catalogue->pere_id = $catal->_id;
      
      //creation de l'id400 
      $idCatalogue = new CIdSante400();
      $idCatalogue->tag = $remote_name;
      $idCatalogue->id400 = substr(hash('md5',$chapitre), 0, 4);
      
      $idCatalogue->bindObject($catalogue);

      $AppUI->stepAjax("Catalogue '$catalogue->libelle' importé", UI_MSG_OK);
      
      // on met a jour $catalogues
      $catalogues[$path] = $catalogue;
      	    	
    }
    
    $catalogue = $catalogues[$path];
    // si il y a un sous chapitre a creer==> le pere du sous chapitre est $catalogue->_id;
    $sschapitre = (string) $_analyse->sschapitre;
    
    if($sschapitre){
      // modification du path
      $path .= $sschapitre;
      
      
      $cataloguessChap = new CCatalogueLabo();
      
      if(!array_key_exists($path,$catalogues)){
        // creation du catalogue
        $cataloguessChap->identifiant = substr(hash('md5',$sschapitre), 0, 4);  // libelle modifié par hash;
        $cataloguessChap->libelle = $sschapitre;
        $cataloguessChap->pere_id = $catalogue->_id;
        //creation de l'id400
        $idCatalogue = new CIdSante400();
        $idCatalogue->tag = $sschapitre;
        $idCatalogue->id400 = substr(hash('md5', $sschapitre), 0, 4);
       
        $idCatalogue->bindObject($cataloguessChap);
        $AppUI->stepAjax("Sous Catalogue '$cataloguessChap->libelle' importé", UI_MSG_OK);
      
        //on met à jour les catalogues
        $catalogues[$path] = $cataloguessChap;
      }
    
      $catalogue = $catalogues[$path];
    }
    // Code de l'analyse
    $catAtt = $_analyse->attributes();
  	$code = $catAtt["code"];
  	
  	$idAnalyse = new CIdSante400();
  	$idAnalyse->tag = $remote_name;
  	$idAnalyse->id400 = (string) $code;
  	
  	
    $analyse = new CExamenLabo();
    $analyse->identifiant = (string) $code;
    $analyse->libelle = (string) $_analyse->libelle;

    $analyse->catalogue_labo_id = $catalogue->_id;
    $analyse->type = "num";
  	
    $idAnalyse->bindObject($analyse);
    $AppUI->stepAjax("Analyse '$analyse->identifiant' importée", UI_MSG_OK);
  }// fin du foreach
}


// Check import configuration
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


// Check imported catalogue document
$doc = new CMbXMLDocument;

if (!$doc->loadXML($content)) {
  $AppUI->stepAjax("Document is not well formed", UI_MSG_ERROR);
}

$tmpPath = "tmp/dPlabo/import_catalogue.xml";
CMbPath::forceDir(dirname($tmpPath));
$doc->save($tmpPath);
$doc->load($tmpPath);

if (!$doc->schemaValidate("modules/$m/remote/catalogue.xsd")) {
  $AppUI->stepAjax("Document is not valid", UI_MSG_ERROR);
}

$AppUI->stepAjax("Document is valid", UI_MSG_OK);

// Check access to idSante400
$canSante400 = CModule::getCanDo("dPsante400");
if (!$canSante400->edit) {
  $AppUI->stepAjax("No permission for module 'dPsante400' or module not installed", UI_MSG_ERROR);
}

// Import catalogue
$cat = new SimpleXMLElement($content);
try {
  importCatalogue($cat);
} 
catch (Exception $e) {
  mbTrace($e);
  $AppUI->stepAjax("Couldn't import catalogue for the  reason stated above", UI_MSG_ERROR);
}

?>