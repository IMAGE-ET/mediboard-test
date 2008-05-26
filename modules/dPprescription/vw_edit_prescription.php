<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

global $AppUI, $can, $dialog;
$can->needsRead();

$filter = new CPrescription();
$filter->prescription_id = mbGetValueFromGetOrSession("prescription_id");
$filter->object_class    = mbGetValueFromGet("object_class", "CSejour");
$filter->object_id       = mbGetValueFromGet("object_id");
$filter->type            = mbGetValueFromGet("type", "externe");
$filter->praticien_id    = mbGetValueFromGet("praticien_id");
$filter->loadRefsFwd();
$filter->_ref_object->loadRefsFwd();

$protocoles_praticien = array();
$protocoles_function = array();


$popup = mbGetValueFromGet("popup");
$categories = array();

if(!$filter->prescription_id && $filter->object_id && $filter->object_class && $filter->praticien_id && $filter->type){
	$prescription = new CPrescription();
	$prescription->object_id = $filter->object_id;
	$prescription->object_class = $filter->object_class;
	
	// Si prescription CSejour
	if($filter->object_class == "CSejour"){
		// Chargement du user_courant
		$user_courant = new CMediusers();
		$user_courant->load($AppUI->user_id);
		
		// Chargement du sejour
  	$sejour = new CSejour();
  	$sejour->load($filter->object_id);	  	
		  	
		if($filter->type == "pre_admission" || $filter->type == "sortie"){
			if($user_courant->isPraticien()){
		  	$prescription->praticien_id = $AppUI->user_id;
		  } else {
		  	$prescription->praticien_id = $sejour->praticien_id;
		  }
	  }
	  if($filter->type ==  "sejour"){
	  	$prescription->praticien_id = $sejour->praticien_id;
	  }
	} else {
		$prescription->praticien_id = $filter->praticien_id;
	}
	

	$prescription->type = $filter->type;
	$prescription->loadMatchingObject();
  $prescription->function_id = "";
	if(!$prescription->_id){
		$prescription->store();
	}
} else {
	// Chargement de la prescription demandé
  $prescription = new CPrescription();
  $prescription->load($filter->_id);
}


$protocoles = array();
$listFavoris = array();
$praticien = new CMediusers();


// Cas d'un protocole => il faut selectionner un object
if($prescription->_id && !$prescription->object_id){
	$prescription->_id = "";
}

if (!$prescription->_id) {
  $prescription->object_class = $filter->object_class;
  $prescription->object_id    = $filter->object_id;
}


if ($prescription->object_id) {
  $prescription->loadRefsFwd();
  $prescription->_ref_object->loadRefSejour();
  $prescription->_ref_object->loadRefPatient();
  $prescription->_ref_object->_ref_patient->loadRefDossierMedical();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->updateFormFields();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->loadRefsAntecedents();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->loadRefsTraitements();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->loadRefsAddictions();
  $prescription->_ref_object->loadRefsPrescriptions();

  // Chargement des protocoles du praticiens
  $protocole = new CPrescription();
  $where = array();
  $where["praticien_id"] = " = '$prescription->_current_praticien_id'";
  $where["object_id"] = "IS NULL";
  $protocoles_praticien = $protocole->loadList($where);
  
  // Chargement des protocoles de la fonction
  $function_id = $prescription->_ref_current_praticien->function_id;
  $where = array();
  $where["function_id"] = " = '$function_id'";
  $where["object_id"] = "IS NULL";
  $protocoles_function = $protocole->loadList($where);
}


// Liste des alertes
$listProduits = array();
$alertesAllergies    = array();
$alertesInteractions = array();
$alertesIPC          = array();
$alertesProfil       = array();
if ($prescription->_id) {
  // Chargement des medicaments et commentaire
  $prescription->loadRefsLinesMedComments();
  
  // Chargement des elements et commentaires
  $prescription->loadRefsLinesElementsComments();
  
  $prescription->loadRefPraticien();
  
  // Gestion des alertes
  $allergies    = new CBcbControleAllergie();
  $allergies->setPatient($prescription->_ref_object->_ref_patient);
  $interactions = new CBcbControleInteraction();
  $IPC          = new CBcbControleIPC();
  $profil       = new CBcbControleProfil();
  $profil->setPatient($prescription->_ref_object->_ref_patient);
  
  foreach ($prescription->_ref_prescription_lines as &$line) {
  	if(!$line->child_id){
	    // Chargement de la posologie
	    // Ajout des produits pour les alertes
	    $allergies->addProduit($line->code_cip);
	    $interactions->addProduit($line->code_cip);
	    $IPC->addProduit($line->code_cip);
	    $profil->addProduit($line->code_cip);
  	}
  }
  $alertesAllergies    = $allergies->getAllergies();
  $alertesInteractions = $interactions->getInteractions();
  $alertesIPC          = $IPC->getIPC();
  $alertesProfil       = $profil->getProfil();
  foreach ($prescription->_ref_prescription_lines as &$line) {
  	if(!$line->child_id){
	    $line->checkAllergies($alertesAllergies);
	    $line->checkInteractions($alertesInteractions);
	    $line->checkIPC($alertesIPC);
	    $line->checkProfil($alertesProfil);
  	}
  }

  // Liste des favoris
  $listFavoris = CPrescription::getFavorisPraticien($prescription->_current_praticien_id);
  
  // Chargement du praticien
  $praticien->load($prescription->praticien_id);


  // Chargement des categories pour chaque chapitre
  $category = new CCategoryPrescription();
  $categories = $category->loadCategoriesByChap();
}

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
	  	//$line->loadRefUserArret();
      $line->loadRefLogDateArret();
      //$line->loadRefLogSignee();
      
	  	$line->loadRefPraticien();
		}
	}
}


// Chargement du poids du patient
$poids = "";
if($prescription->_id){
	if($prescription->_ref_object->_class_name == "CSejour"){
		$consult_anesth = new CConsultAnesth();
		$consult_anesth->sejour_id = $prescription->_ref_object->_id;
		$consult_anesth->loadMatchingObject();
		
		if($consult_anesth->_id){
		  $poids = $consult_anesth->poid;
		}
		
		// Chargement des dates de l'operations
    $sejour =& $prescription->_ref_object;
    $sejour->makeDatesOperations();
	}
	$prescription->countLinesMedsElements();
}

// Liste des praticiens
$user = new CMediusers();
$listPrats = $user->loadPraticiens(PERM_EDIT);

// Chargement du user_courant
$user->load($AppUI->user_id);
$is_praticien = $user->isPraticien();

$protocole_line = new CPrescriptionLineMedicament();
$protocole_line->debut = mbDate();

$contexteType = array();
$contexteType["CConsultation"][] = "externe";
$contexteType["CSejour"][] = "pre_admission";
$contexteType["CSejour"][] = "sortie";
$contexteType["CSejour"][] = "sejour";
$contexteType["CSejour"][] = "traitement";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("is_praticien", $is_praticien);
$smarty->assign("protocole_line", $protocole_line);
$smarty->assign("poids", $poids);
$smarty->assign("today", mbDate());
$smarty->assign("refresh_pharma", "0");
$smarty->assign("mode_pharma", "0");
$smarty->assign("contexteType"       , $contexteType);
$smarty->assign("today"              , mbDate());
$smarty->assign("categories"         , $categories);
$smarty->assign("executants"         , $executants);
$smarty->assign("prise_posologie"    , new CPrisePosologie());
$smarty->assign("categories"         , $categories);
$smarty->assign("category"           , "medicament");
$smarty->assign("alertesAllergies"   , $alertesAllergies);
$smarty->assign("alertesInteractions", $alertesInteractions);
$smarty->assign("alertesIPC"         , $alertesIPC);
$smarty->assign("alertesProfil"      , $alertesProfil);

$smarty->assign("prescription", $prescription);
$smarty->assign("filter"      , $filter);
$smarty->assign("listPrats"   , $listPrats);
$smarty->assign("listFavoris", $listFavoris);
$smarty->assign("protocoles_praticien", $protocoles_praticien);
$smarty->assign("protocoles_function", $protocoles_function);

$smarty->assign("class_category", new CCategoryPrescription());
$smarty->assign("mode_sejour", 0);
$smarty->assign("praticien", $praticien);
$smarty->assign("moments", $moments);
if($dialog == 1) {
  $smarty->display("vw_edit_prescription_popup.tpl");
} else {
  $smarty->display("vw_edit_prescription.tpl");
  
}

?>