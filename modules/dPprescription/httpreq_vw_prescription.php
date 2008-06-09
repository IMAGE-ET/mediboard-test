<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */


global $AppUI, $can, $m;

$can->needsRead();

$mbProduit = new CBcbProduit();
$produits = $mbProduit->searchProduitAutocomplete("effe", 10);

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$object_class    = mbGetValueFromGet("object_class");
$object_id       = mbGetValueFromGet("object_id");
$mode_pharma     = mbGetValueFromGet("mode_pharma", 0);
$refresh_pharma  = mbGetValueFromGet("refresh_pharma", 0);
$mode_protocole  = mbGetValueFromGetOrSession("mode_protocole", 0);
$full_mode       = mbGetValueFromGet("full_mode", 0);
$sejour_id       = mbGetValueFromGetOrSession("sejour_id");
$type            = mbGetValueFromGetOrSession("type");
$element_id      = mbGetValueFromGetOrSession("element_id");
$category        = mbGetValueFromGetOrSession("category_name");

// Initialisations
$protocoles_praticien = array();
$protocoles_function  = array();
$listFavoris          = array();
$poids                = "";
$alertesAllergies     = array();
$alertesInteractions  = array();
$alertesIPC           = array();
$alertesProfil        = array();
$favoris              = array();
$listProduits         = array();

//$perm_create_line     = 0;
$perm_create_line     = 1;


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


// Calcul de la div à rafraichir
if ($element_id){
  $element = new CElementPrescription();
  $element->load($element_id);
  $element->loadRefCategory();
  $category = $element->_ref_category_prescription->chapitre;
}


// Chargement de la prescription
$prescription = new CPrescription();
if($prescription_id){
  $prescription->load($prescription_id);
}

// Si pas de prescription_id et presence d'un sejour_id => chargement de la prescription de sejour
$prescriptions_sejour = array();
if(!$prescription->_id && $sejour_id){
	$prescription_sejour = new CPrescription();
	$where = array();
  $where["object_id"] = " = '$sejour_id'";
  $where["object_class"] = " = 'CSejour'";
  $where["type"] = " != 'traitement'";
  $order = "prescription_id DESC";
  $prescriptions_sejour = $prescription_sejour->loadList($where, $order);
  if(count($prescriptions_sejour)){
    $prescription =& end($prescriptions_sejour);
  }
  foreach($prescriptions_sejour as $_prescription_sejour){
  	if($_prescription_sejour->type == "sejour"){
  		$prescription =& $_prescription_sejour;
  		break;
  	}
  }
}


// Si tous les elements sont passés, on charge la prescription (cas de la prescription externe)
// Permet de ne pas recreer une prescription si elle existe déja... en cas de non rechargement de la widget
if(!$prescription_id && $object_class && $object_id && $type){
	$full_mode = 1;
  $prescription = new CPrescription();
  $prescription->object_id = $object_id;
  $prescription->object_class = $object_class;
  $prescription->type = $type;	
  $prescription->loadMatchingObject();
}

// Chargement des lignes de la prescription
if($prescription->_id){
  $prescription->loadRefsLinesMedComments();
  $prescription->loadRefsLinesElementsComments();
  
  // Chargement de la prescription de traitement
  $object =& $prescription->_ref_object;
  $object->loadRefPrescriptionTraitement();
  $prescription_traitement =& $object->_ref_prescription_traitement;
	if($prescription_traitement->_id){
		$prescription_traitement->loadRefsLines();
		foreach($prescription_traitement->_ref_prescription_lines as &$line){
		  $line->loadRefsPrises();
	  	$line->loadRefLogDateArret();
	  	$line->loadRefPraticien();
	  }
	}
	
  // Calcul du nombre d'elements dans la prescription
	$prescription->countLinesMedsElements();
}




if($prescription->object_id && $prescription->_id) {
	// Chargement des fovoris 
  $listFavoris = CPrescription::getFavorisPraticien($prescription->_current_praticien_id);
  
	// Chargement du patient
  $object->loadRefPatient();
	$patient =& $object->_ref_patient;
	$patient->loadRefDossierMedical();
  
	$object->loadRefsPrescriptions();
	
	// Chargement du dossier medicam
  $dossier_medical =& $patient->_ref_dossier_medical;
  $dossier_medical->updateFormFields();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->loadRefsTraitements();
  $dossier_medical->loadRefsAddictions();
  
  // Calcul des alertes de la prescription
  $allergies    = new CBcbControleAllergie();
  $allergies->setPatient($prescription->_ref_object->_ref_patient);
  $interactions = new CBcbControleInteraction();
  $IPC          = new CBcbControleIPC();
  $profil       = new CBcbControleProfil();
  $profil->setPatient($prescription->_ref_object->_ref_patient);
  
  $lines = array();
  $lines["prescription"] = $prescription->_ref_prescription_lines;
  if($prescription_traitement->_id){
    $lines["traitement"] = $prescription_traitement->_ref_prescription_lines;
  }
  
  foreach($lines as $type => $type_line){
	  foreach($type_line as &$line) {
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
  
  foreach($lines as $type_line){
    foreach($type_line as &$line) {
      $line->checkAllergies($alertesAllergies);
      $line->checkInteractions($alertesInteractions);
      $line->checkIPC($alertesIPC);
      $line->checkProfil($alertesProfil);
    }
  }

	// Chargement du poids du patient
	$patient->loadRefConstantesMedicales();
  $constantes_medicales = $patient->_ref_constantes_medicales;
  $poids = $constantes_medicales->poids;

  if($object->_class_name == "CSejour"){
    $object->makeDatesOperations();
    foreach($object->_dates_operations as $date){
      $prescription->_dates_dispo[] = $date;
    }
    $prescription->_dates_dispo[] = mbDate($object->_entree);
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


if($full_mode && $prescription->_id){
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

/*
if($is_praticien){
	$perm_create_line = 1;
}

if(!$is_praticien){
	$time = mbTime();
	//$time = "20:01:00";
	
	$borne_start = CAppUI::conf("dPprescription CPrescription infirmiere_borne_start");
	$borne_stop = CAppUI::conf("dPprescription CPrescription infirmiere_borne_stop");
	
	$borne_start .= ":00:00";
	$borne_stop .= ":00:00";

	$freeDays = mbBankHolidays();
  if(array_key_exists(mbDate(),$freeDays) || ($time >= $borne_start) || ($time <= $borne_stop)){
  	$perm_create_line = 1;
  }
}
*/
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("perm_create_line"   , $perm_create_line);
$smarty->assign("contexteType"       , $contexteType);
$smarty->assign("httpreq"            , 1);
$smarty->assign("sejour_id"          , $sejour_id);
$smarty->assign("is_praticien"       , $is_praticien);
$smarty->assign("today"              , mbDate());
$smarty->assign("poids"              , $poids);
$smarty->assign("categories"         , $categories);
$smarty->assign("executants"         , $executants);
$smarty->assign("moments"            , $moments);
$smarty->assign("prise_posologie"    , new CPrisePosologie());
$smarty->assign("protocole"          , new CPrescription());
$smarty->assign("alertesAllergies"   , $alertesAllergies);
$smarty->assign("alertesInteractions", $alertesInteractions);
$smarty->assign("alertesIPC"         , $alertesIPC);
$smarty->assign("alertesProfil"      , $alertesProfil);
$smarty->assign("prescription"       , $prescription);
$smarty->assign("listPrats"          , $listPrats);
$smarty->assign("listFavoris"        , $listFavoris);
$smarty->assign("category"           , $category);
$smarty->assign("categories"         , $categories);
$smarty->assign("class_category"     , new CCategoryPrescription());
$smarty->assign("refresh_pharma"     , $refresh_pharma);
$smarty->assign("mode_pharma"        , $mode_pharma);
$smarty->assign("full_mode"          , $full_mode);
$smarty->assign("protocole_line"     , $protocole_line);
$smarty->assign("mode_protocole"     , $mode_protocole);
$smarty->assign("prescriptions_sejour", $prescriptions_sejour);

if($full_mode){
	$_sejour = new CSejour();
	$_sejour->load($sejour_id);
  $smarty->assign("protocoles_praticien", $protocoles_praticien);
  $smarty->assign("protocoles_function", $protocoles_function);
	$smarty->assign("praticien_sejour", $_sejour->praticien_id);
	//$smarty->assign("full_mode", 1);
	$smarty->display("vw_edit_prescription_popup.tpl");
	return;
}

if($mode_protocole){
	$smarty->assign("praticiens", $praticiens);
	$smarty->assign("functions", $functions);
	$smarty->assign("category", "medicament");
	$smarty->display("inc_vw_prescription.tpl");
}

// Premier chargement de la pharmacie
if($mode_pharma && $refresh_pharma){
  $smarty->assign("praticien", $prescription->_ref_praticien);
  $smarty->display("inc_vw_prescription.tpl");	
}
	  	
if(!$refresh_pharma && !$mode_protocole){
	// Refresh Pharma
	if($mode_pharma){
		$category = "medicament";
	  $smarty->display("inc_div_medicament.tpl");
	} else {
	  // Refresh Protocole
    if(!$category){
	  	$smarty->display("inc_vw_produits_elements.tpl");	
    } else {
      // Refresh Medicament
      if($category == "medicament"){
       	$smarty->display("inc_div_medicament.tpl");
      } 
      // refresh Element
      else {
        $smarty->assign("element", $category);
        $smarty->display("inc_div_element.tpl");
      }
    }
	}
}

?>