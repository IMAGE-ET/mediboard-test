<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $dPconfig, $g;

$can->needsRead();

$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);

$showCount = 30;

// L'utilisateur est-il un chirurgien
$chir = $mediuser->isFromType(array("Chirurgien")) ? $mediuser : null;

// L'utilisateur est-il un anesthésiste
$anesth = $mediuser->isFromType(array("Anesthésiste")) ? $mediuser : null;

// Chargement du patient sélectionné
$patient_id = mbGetValueFromGetOrSession("patient_id");
$patient = new CPatient;
if ($new = mbGetValueFromGet("new")) {
  $patient->load(null);
  mbSetValueToSession("patient_id", null);
  mbSetValueToSession("selClass", null);
  mbSetValueToSession("selKey", null);
} else {
  $patient->load($patient_id);
}


// Récuperation des patients recherchés
$patient_nom         = mbGetValueFromGetOrSession("nom"         , ""       );
$patient_prenom      = mbGetValueFromGetOrSession("prenom"      , ""       );
$patient_jeuneFille  = mbGetValueFromGetOrSession("jeuneFille"  , ""       );
$patient_ville       = mbGetValueFromGetOrSession("ville"       , ""       );
$patient_cp          = mbGetValueFromGetOrSession("cp"          , ""       );
$patient_day         = mbGetValueFromGet("Date_Day"    , "");
$patient_month       = mbGetValueFromGet("Date_Month"  , "");
$patient_year        = mbGetValueFromGet("Date_Year"   , "");
$patient_naissance   = null;
$patient_ipp         = mbGetValueFromGet("patient_ipp");
$useVitale           = mbGetValueFromGet("useVitale",  CAppUI::pref('GestionFSE') && CAppUI::pref('VitaleVision') ? 1 : 0);

$patVitale = new CPatient();
  
// Recherche par IPP
if($patient_ipp && !$useVitale && CModule::getInstalled("dPsante400")){
  // Initialisation dans le cas d'une recherche par IPP
  $patients = array();
  $patientsSoundex = array();
  $patientsCount = 0;
  $patientsSoundexCount = 0;
  
  $idsante = new CIdSante400();
  $idsante->tag = str_replace('$g',$g, $dPconfig["dPpatients"]["CPatient"]["tag_ipp"]);
  $idsante->id400 = $patient_ipp;
  $idsante->object_class = "CPatient";
  $idsante->loadMatchingObject();
  
  if ($idsante->object_id){
   $patient = new CPatient();
   $patient->load($idsante->object_id);
   mbSetValueToSession("patient_id", $patient->_id);
   $patients[$patient->_id] = $patient; 
  }
} 
else {
	// Champs vitale
	if ($useVitale && CAppUI::pref('GestionFSE') && !CAppUI::pref('VitaleVision')) {
	  $patVitale->getValuesFromVitale();
	  $patVitale->updateFormFields();
	  $patient_nom    = $patVitale->nom;
	  $patient_prenom = $patVitale->prenom;
	  mbSetValueToSession("nom", $patVitale->nom);
	  mbSetValueToSession("prenom", $patVitale->prenom);
	  $patient_naissance = "on";
	  mbSetValueToSession("naissance", "on");
	  $patVitale->loadFromIdVitale();
	}
	
	$where        = array();
	$whereSoundex = array();
	$soundexObj   = new soundex2();
	
	if ($patient_nom) {
	  $patient_nom = trim($patient_nom);
	  $where["nom"]                 = "LIKE '$patient_nom%'";
	  $whereSoundex["nom_soundex2"] = "LIKE '".$soundexObj->build($patient_nom)."%'";
	}
	if ($patient_prenom) {
	  $patient_prenom = trim($patient_prenom);
	  $where["prenom"]                 = "LIKE '$patient_prenom%'";
	  $whereSoundex["prenom_soundex2"] = "LIKE '".$soundexObj->build($patient_prenom)."%'";
	}
	if ($patient_jeuneFille) {
	  $patient_jeuneFille = trim($patient_jeuneFille);
	  $where["nom_jeune_fille"]        = "LIKE '$patient_jeuneFille%'";
	  $whereSoundex["nomjf_soundex2"]  = "LIKE '".$soundexObj->build($patient_jeuneFille)."%'";
	}
	
	
	if(($patient_year) || ($patient_month) || ($patient_day)){
		$patient_naissance = "on";
	}
	
	if ($patient_naissance == "on"){
	  $year =($patient_year)?"$patient_year-":"%-";
	  $month =($patient_month)?"$patient_month-":"%-";
	  $day =($patient_day)?"$patient_day":"%";
	  if ($day!="%") {
	    $day = str_pad($day,2,"0",STR_PAD_LEFT);
	  }
	  
	  $naissance = $year.$month.$day;
	  
	  if($patient_year || $patient_month || $patient_day){
	    $where["naissance"] = $whereSoundex["naissance"] = "LIKE '$naissance'";
	  }
	}
	
	if ($patient_ville) $where["ville"] = $whereSoundex["ville"] = "LIKE '$patient_ville%'";
	if ($patient_cp)    $where["cp"]    = $whereSoundex["cp"]    = "= '$patient_cp'";
	
	$patients        = array();
	$patientsSoundex = array();
	
	$order = "nom, prenom, naissance";
	$pat = new CPatient();
	
	// Patient counts
	$patientsCount = $where ? $pat->countList($where) : 0;
	$patientsSoundexCount = $whereSoundex ? $pat->countList($whereSoundex) : 0;
	$patientsSoundexCount -= $patientsCount;
	
	// Chargement des patients
	if ($where) {
	  $patients = $pat->loadList($where, $order, "0, $showCount");
	}
	
	if ($whereSoundex) {
	  $patientsSoundex = $pat->loadList($whereSoundex, $order, "0, $showCount");
	  $patientsSoundex = array_diff_key($patientsSoundex, $patients);
	}
	
	// Sélection du premier de la liste si aucun n'est déjà sélectionné
	if (!$patient->_id and count($patients) == 1) {
	  $patient = reset($patients);
	}
	
	// Patient vitale associé trouvé : prioritaire
	if ($patVitale->_id) {
	  $patient = $patVitale;
	  // Au cas où il n'aurait pas été trouvé grâce aux champs
	  $patients[$patient->_id] = $patient; 
	}
}

$listPrat = array();
if ($patient->_id) {
  // Liste des praticiens disponibles
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_EDIT);
  $patient->loadDossierComplet();
  foreach($patient->_ref_sejours as $key=>$sejour){
  	$sejour->loadNumDossier();
  }
}

// Chargement des identifiants standards
$patient->loadIPP();
$patient->loadIdVitale();


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dPsanteInstalled", CModule::getInstalled("dPsante400"));
$smarty->assign("canPatients"     , CModule::getCanDo("dPpatients")    );
$smarty->assign("canAdmissions"   , CModule::getCanDo("dPadmissions")  );
$smarty->assign("canPlanningOp"   , CModule::getCanDo("dPplanningOp")  );
$smarty->assign("canCabinet"      , CModule::getCanDo("dPcabinet")     );

$smarty->assign("nom"                 , $patient_nom              );
$smarty->assign("prenom"              , $patient_prenom           );
$smarty->assign("jeuneFille"          , $patient_jeuneFille       );
$smarty->assign("naissance"           , $patient_naissance        );
$smarty->assign("ville"               , $patient_ville            );
$smarty->assign("cp"                  , $patient_cp               );

$smarty->assign("useVitale"           , $useVitale                );
$smarty->assign("patVitale"           , $patVitale                );
$smarty->assign("patients"            , $patients                 );
$smarty->assign("patientsSoundex"     , $patientsSoundex          );
$smarty->assign("patientsCount"       , $patientsCount            );
$smarty->assign("patientsSoundexCount", $patientsSoundexCount     );

$smarty->assign("patient"             , $patient                  );
$smarty->assign("chir"                , $chir                     );
$smarty->assign("anesth"              , $anesth                   );
$smarty->assign("listPrat"            , $listPrat                 );
$smarty->assign("board"               , 0                         );
$smarty->assign("patient_ipp"         , $patient_ipp              );

$smarty->display("vw_idx_patients.tpl");

?>