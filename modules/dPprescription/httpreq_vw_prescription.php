<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

global $AppUI, $can, $m;

$mbProduit = new CBcbProduit();
$produits = $mbProduit->searchProduitAutocomplete("effe", 10);


$can->needsRead();

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$object_class    = mbGetValueFromGetOrSession("object_class");
$object_id       = mbGetValueFromGetOrSession("object_id");

$mode_pharma = mbGetValueFromGet("mode_pharma", 0);
$refresh_pharma = mbGetValueFromGet("refresh_pharma", 0);
$mode_protocole  = mbGetValueFromGetOrSession("mode_protocole", 0);

$listFavoris = array();
$element_id = mbGetValueFromGetOrSession("element_id");
$category_name = mbGetValueFromGetOrSession("category_name");

$category = null;
$poids = "";

if ($element_id){
  $element = new CElementPrescription();
  $element->load($element_id);
  $element->loadRefCategory();
  $category = $element->_ref_category_prescription->chapitre;
}

if(!$element_id && $category_name){
  $category = $category_name;
}

// Liste des alertes
$alertesAllergies    = array();
$alertesInteractions = array();
$alertesIPC          = array();
$alertesProfil       = array();
$favoris             = array();



// Chargement de la catégorie demandé
$prescription = new CPrescription();
$prescription->load($prescription_id);


$listProduits = array();

if(!$prescription->_id) {
  $prescription->object_class = $object_class;
  $prescription->object_id    = $object_id;
} else {
  // Liste des favoris
  if($prescription->praticien_id){
    $listFavoris = CPrescription::getFavorisPraticien($prescription->praticien_id);  
  }
}


if($prescription->_id){
	// Chargement des medicaments et commentaire
  $prescription->loadRefsLinesMedComments();
  
  // Chargement des elements et commentaires
  $prescription->loadRefsLinesElementsComments();
}


if($prescription->object_id) {
	$prescription->loadRefsFwd();
	$prescription->_ref_object->loadRefSejour();
	$prescription->_ref_object->loadRefPatient();
	$patient =& $prescription->_ref_object->_ref_patient;
  $patient->loadRefDossierMedical();
  
  $dossier_medical =& $patient->_ref_dossier_medical;
  
  $dossier_medical->updateFormFields();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->loadRefsTraitements();
  $dossier_medical->loadRefsAddictions();
  
  // Calcul des alertes
  $allergies    = new CBcbControleAllergie();
  $allergies->setPatient($prescription->_ref_object->_ref_patient);
  $interactions = new CBcbControleInteraction();
  $IPC          = new CBcbControleIPC();
  $profil       = new CBcbControleProfil();
  $profil->setPatient($prescription->_ref_object->_ref_patient);
  foreach($prescription->_ref_prescription_lines as &$line) {
    // Ajout des produits pour les alertes
    $allergies->addProduit($line->code_cip);
    $interactions->addProduit($line->code_cip);
    $IPC->addProduit($line->code_cip);
    $profil->addProduit($line->code_cip);
  }
  $alertesAllergies    = $allergies->getAllergies();
  $alertesInteractions = $interactions->getInteractions();
  $alertesIPC          = $IPC->getIPC();
  $alertesProfil       = $profil->getProfil();
  foreach($prescription->_ref_prescription_lines as &$line) {
    $line->checkAllergies($alertesAllergies);
    $line->checkInteractions($alertesInteractions);
    $line->checkIPC($alertesIPC);
    $line->checkProfil($alertesProfil);
  }

  
}
	
// Chargement des categories pour chaque chapitre
$categoryPresc = new CCategoryPrescription();
$categories = $categoryPresc->loadCategoriesByChap();

// Chargement de la liste des moments
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();
$executants = CExecutantPrescriptionLine::getAllExecutants();

// Chargement de toutes les categories
$categorie = new CCategoryPrescription();
$cats = $categorie->loadList();
foreach($cats as $key => $cat){
	$categories["cat".$key] = $cat;
}

	
// Chargement des traitement de la prescription
if($prescription->_id){
	$prescription->_ref_object->loadRefPrescriptionTraitement();
	if($prescription->_ref_object->_ref_prescription_traitement->_id){
		$prescription->_ref_object->_ref_prescription_traitement->loadRefsLines();
		foreach($prescription->_ref_object->_ref_prescription_traitement->_ref_prescription_lines as &$line){
		  $line->loadRefsPrises();
	  	$line->loadRefLogDateArret();
	  	$line->loadRefPraticien();
	  }
	}
		
	// Chargement du poids du patient
	if($prescription->_ref_object->_class_name == "CSejour"){
		$consult_anesth = new CConsultAnesth();
		$consult_anesth->sejour_id = $prescription->_ref_object->_id;
		$consult_anesth->loadMatchingObject();
		
		if($consult_anesth->_id){
		  $poids = $consult_anesth->poid;
		}
	}
}

if($mode_protocole){
	// Chargement de la liste des praticiens
  $praticien = new CMediusers();
  $praticiens = $praticien->loadPraticiens();

  // Chargement des functions
  $function = new CFunctions();
  $functions = $function->loadSpecialites(PERM_EDIT);
}

// Liste des praticiens
$user = new CMediusers();
$listPrats = $user->loadPraticiens(PERM_EDIT);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("httpreq", 1);

$smarty->assign("today"              , mbDate());
$smarty->assign("poids", $poids);
$smarty->assign("categories", $categories);
$smarty->assign("executants", $executants);
$smarty->assign("moments", $moments);

$smarty->assign("prise_posologie", new CPrisePosologie());
$smarty->assign("protocole", new CPrescription());
$smarty->assign("alertesAllergies"   , $alertesAllergies);
$smarty->assign("alertesInteractions", $alertesInteractions);
$smarty->assign("alertesIPC"         , $alertesIPC);
$smarty->assign("alertesProfil"      , $alertesProfil);

$smarty->assign("prescription", $prescription);
$smarty->assign("listPrats"   , $listPrats);
$smarty->assign("listFavoris" , $listFavoris);
$smarty->assign("category"    , $category);
$smarty->assign("categories"  , $categories);
$smarty->assign("class_category", new CCategoryPrescription());
$smarty->assign("refresh_pharma", $refresh_pharma);



if($mode_protocole){
	$smarty->assign("praticiens", $praticiens);
	$smarty->assign("functions", $functions);
	$smarty->assign("mode_pharma", "0");
	$smarty->assign("mode_protocole", "1");
	$smarty->assign("category", "medicament");
	$smarty->display("inc_vw_prescription.tpl");
} 

if($mode_pharma && $refresh_pharma){
  $smarty->assign("mode_protocole", "0");
  $smarty->assign("mode_pharma", "1");
  $smarty->assign("praticien", $prescription->_ref_praticien);
  $smarty->display("inc_vw_prescription.tpl");	
}
	
  	
if(!$refresh_pharma && !$mode_protocole){
	if($mode_pharma){
		$category_name = "medicament";
	  $smarty->assign("mode_pharma", "1");
	  $smarty->display("inc_div_medicament.tpl");
	} else {
	  $smarty->assign("mode_pharma", "0");
	  // Cas de la selection d'un protocole
    if(!$category_name){
      $smarty->assign("mode_protocole", "0");
	  	$smarty->display("inc_vw_produits_elements.tpl");	
    } else {
      // Cas du rafraichissement de la partie medicament
      if($category_name == "medicament"){
       	$smarty->display("inc_div_medicament.tpl");
      } else {
      	// Cas du rafraichissement d'une partie element
        $smarty->assign("element", $category_name);
        $smarty->display("inc_div_element.tpl");
      }
    }
	}
}





?>