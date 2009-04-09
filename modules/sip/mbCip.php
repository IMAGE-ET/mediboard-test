<?php
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision$
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g, $m, $AppUI;

$can->needsRead();

$format = CAppUI::conf('sip soap rooturl');

if (preg_match('#\%u#', $format)) {
	$format = str_replace('%u', CAppUI::conf('sip soap user'), $format);
}

if (preg_match('#\%p#', $format)) {
  $format = str_replace('%p', CAppUI::conf('sip soap pass'), $format);
}
mbTrace($format);
$format = str_replace('%id', str_pad($this->_id?$this->_id:0, 8, '0', STR_PAD_LEFT), $format);


die;

CAppUI::requireModuleClass("sip", "hprimxmlevenementspatients");
CAppUI::requireModuleClass("sip", "hprimxmlacquittementspatients");

$username = mbGetValueFromPost('username');
$password = mbGetValueFromPost('_user_password');

$domEvenement = new CHPrimXMLAcquittementsPatients();
$domEvenement->generateFromOperation($mbObject);
$doc_valid = $domEvenement->schemaValidate();
$domEvenement->saveTempFile();

// première étape : désactiver le cache lors de la phase de test
ini_set("soap.wsdl_cache_enabled", "0");

// lier le client au fichier WSDL
$clientSOAP = new SoapClient(CAppui::conf("base_url")."/index.php?login=1&username=".$username."&password=".$password."&m=sip&a=mbSip&suppressHeaders=1&wsdl");

$domEvenement = new CHPrimXMLDocument("evenementPatient", "msgEvenementsPatients105", $m);
$domEvenement->load(CAppui::conf("base_url")."/modules/sip/hprim/evenementPatient/document.xml");

$newPatient = new CPatient();

$xpath = new CMbXPath($domEvenement);
$xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );

$query = "/hprim:evenementsPatients/hprim:enteteMessage";

$entete = $xpath->queryUniqueNode($query);
$agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
$systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents);
$idClient = $xpath->queryTextNode("hprim:code", $systeme);

$query = "/hprim:evenementsPatients/hprim:evenementPatient";

$query = "/hprim:evenementsPatients/hprim:evenementPatient";
$evenementPatient = $xpath->queryUniqueNode($query);
$enregistrementPatient = $xpath->queryUniqueNode("hprim:enregistrementPatient", $evenementPatient);
    
$action = $xpath->getActionEvenement($evenementPatient);

$patient = $xpath->queryUniqueNode("hprim:patient", $enregistrementPatient);
$voletMedical = $xpath->queryUniqueNode("hprim:voletMedical", $enregistrementPatient);


$newPatient = $xpath->createPatient($patient, $newPatient);

mbTrace($newPatient);die;

$idSource = $xpath->getIdSource($patient);
$idCible = $xpath->getIdCible($patient);

$cip = new CDestinataireHprim();
$cip->client_id = $idClient;
$cip->loadMatchingObject();
$tagCip = $cip->tag;

$id400 = new CIdSante400();
//Paramétrage de l'id 400
$id400->object_id = $idSource;
$id400->object_class = "CPatient";
$id400->tag = $tagCip;

// Cas 1 : Patient existe sur le SIP
if($id400->loadMatchingObject()) {
	// Cas 1.1 : Pas d'identifiant cible
	if(!$idCible) {
		// Le patient est connu sur le SIP
		if ($newPatient->load($idSource)) {
			// Mapping du patient
		  $newPatient = $xpath->createPatient($patient, $newPatient);
	    // Création de l'IPP
      $IPP = new CIdSante400();
      //Paramétrage de l'id 400
      $IPP->object_class = "CPatient";
      $IPP->tag = CAppUI::conf("dPpatients CPatient tag_ipp");
      if (!$IPP->tag) 
         $IPP->tag = "sipIPP";
    
      // Chargement du dernier id externe de prescription du praticien s'il existe
      $IPP->loadMatchingObject("id400 DESC");
      
      // Incrementation de l'id400
      $IPP->id400++;
      $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);
      
      $IPP->object_id = $newPatient->_id;
      $IPP->_id = null;
      $IPP->last_update = mbDateTime();  
      $IPP->store();
      
      $newPatient->_IPP = $IPP->id400;	

      $newPatient->store();	
		} 
	}  
	// Cas 1.2 : Identifiant cible envoyé
	else {
		$IPP = new CIdSante400();
    //Paramétrage de l'id 400
    $IPP->object_class = "CPatient";
    $IPP->tag = CAppUI::conf("dPpatients CPatient tag_ipp");
    if (!$IPP->tag) 
      $IPP->tag = "sipIPP";

    $IPP->id400 = $idCible;
    $IPP->loadMatchingObject();
    
		$newPatient->_id = $IPP->object_id;
		$newPatient->loadMatchingObject();
		
		// Mapping du patient
    $newPatient = $xpath->createPatient($patient, $newPatient);
		$newPatient->store(); 
	}
} 
// Cas 2 : Patient n'existe pas sur le SIP
else {;
	// Mapping du patient
  $newPatient = $xpath->createPatient($patient, $newPatient);
  $newPatient->store();
  
  // Création de l'identifiant externe TAG CIP + idSource
  $id400Patient = new CIdSante400();
	//Paramétrage de l'id 400
	$id400Patient->object_class = "CPatient";
	$id400Patient->tag = $tagCip;
  
  // Chargement du dernier id externe de prescription du praticien s'il existe
  $id400Patient->loadMatchingObject("id400 DESC");
  
  // Incrementation de l'id400
  $id400Patient->id400++;
  $id400Patient->id400 = str_pad($id400Patient->id400, 6, '0', STR_PAD_LEFT);
  
  $id400Patient->object_id = $newPatient->_id;
  $id400Patient->_id = null;
  $id400Patient->last_update = mbDateTime();  
  $id400Patient->store();
  
  // Création de l'IPP
  $IPP = new CIdSante400();
  //Paramétrage de l'id 400
  $IPP->object_class = "CPatient";
  $IPP->tag = CAppUI::conf("dPpatients CPatient tag_ipp");
  if (!$IPP->tag) 
  	 $IPP->tag = "sipIPP";

  // Chargement du dernier id externe de prescription du praticien s'il existe
  $IPP->loadMatchingObject("id400 DESC");
  
  // Incrementation de l'id400
  $IPP->id400++;
  $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);
  
  $IPP->object_id = $newPatient->_id;
  $IPP->_id = null;
  $IPP->last_update = mbDateTime();  
  $IPP->store();
  
  $newPatient->_IPP = $IPP->id400;
}

mbTrace($newPatient);
die;

// Enregistrement d'un patient avec son identifiant (ipp) dans le système
if ($domEvenement->getElementsByTagName("enregistrementPatient")->length != 0) {
	$idSource = $domEvenement->getElementsByTagName('emetteur')->item(1)->nodeValue;
	$emetteur = $domEvenement->getElementsByTagName('emetteur');
	foreach ($emetteur as $emetteurElement) {
		$child = $emetteurElement->childNodes;
		foreach ($child as $childElement) {
	    $child2 = $childElement->childNodes;
			mbTrace($childElement->nodeName);
		}
	
	   echo "---".$emetteurElement->nodeName.'<br />';
	 //  if ($emetteurElement->
	}
	die;
    if (utf8_decode($racine->getAttributeNode("categorie")) == "système")
      $idClient = $e->nodeValue;
	
  if ($domEvenement->getElementsByTagName('agent')->item(2)->nodeValue) {
  	mbTrace($idClient,"id client");
  }
	
	
	/*
	$id_ext = new CIdSante400();
  $id_ext->object_id = $idSource;
  $id_ect->object_class = "CPatient";
  $id_ext->tag = ""; 
  $id_ext->loadMatchingObject();*/
    
	// Cas 1.1 : Le patient existe sur le SIP, mais pas d'identifiant cible
	if ($domEvenement->getElementsByTagName("recepteur")->length == 0) {
    
	}
	// Cas 1.2 : Le patient existe sur le SIP, et on recoit un identifiant cible
  else {
    $idCible = $domEvenement->getElementsByTagName('recepteur')->item(0)->nodeValue;
  }	
} else {
	echo "autre chose";
}

$messagePatient = utf8_encode($domEvenement->saveXML());
//mbTrace($messagePatient);

// executer la methode evenementPatient
//echo "Methode evenementPatient() <br />";
//echo $clientSOAP->evenementPatient($messagePatient);
//echo "<br /><br />";

?>