<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $g;

$can->needsRead();

$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);

$showCount = 30;

// Chargement du patient sélectionné
$patient_id = CValue::getOrSession("patient_id");
$patient = new CPatient;
if ($new = CValue::get("new")) {
  $patient->load(null);
  CValue::setSession("patient_id", null);
  CValue::setSession("selClass", null);
  CValue::setSession("selKey", null);
} 
else {
  $patient->load($patient_id);
}

// Récuperation des patients recherchés
$patient_nom         = trim(CValue::getOrSession("nom"   ));
$patient_prenom      = trim(CValue::getOrSession("prenom"));
$patient_ville       = CValue::getOrSession("ville" );
$patient_cp          = CValue::getOrSession("cp"    );
$patient_day         = CValue::get("Date_Day"  );
$patient_month       = CValue::get("Date_Month");
$patient_year        = CValue::get("Date_Year" );
$patient_naissance   = null;
$patient_ipp         = CValue::get("patient_ipp");
$useVitale           = CValue::get("useVitale",  CAppUI::pref('GestionFSE') && CAppUI::pref('VitaleVision') ? 1 : 0);

$patient_nom_search    = null;
$patient_prenom_search = null;

$patVitale = new CPatient();
  
// Recherche par IPP
if ($patient_ipp && CModule::getInstalled("dPsante400")){
  // Initialisation dans le cas d'une recherche par IPP
  $patients = array();
  $patientsSoundex = array();
  $patientsCount = 0;
  $patientsSoundexCount = 0;
  
  $idsante = new CIdSante400();
  $idsante->tag = str_replace('$g',$g, CAppUI::conf("dPpatients CPatient tag_ipp"));
  $idsante->id400 = $patient_ipp;
  $idsante->object_class = "CPatient";
  $idsante->loadMatchingObject();
  
  if ($idsante->object_id){
    $patient = new CPatient();
    $patient->load($idsante->object_id);
    CValue::setSession("patient_id", $patient->_id);
    $patients[$patient->_id] = $patient; 
  }
}

// Recheche par traits classiques 
else {
	// Champs vitale
	if ($useVitale && CAppUI::pref('GestionFSE') && !CAppUI::pref('VitaleVision')) {
	  $patVitale->getValuesFromVitale();
	  $patVitale->updateFormFields();
	  $patient_nom    = $patVitale->nom   ;
	  $patient_prenom = $patVitale->prenom;
	  CValue::setSession("nom"   , $patVitale->nom   );
	  CValue::setSession("prenom", $patVitale->prenom);
	  $patVitale->loadFromIdVitale();
	}
	
	$where        = array();
	$whereSoundex = array();
	$soundexObj   = new soundex2();
	
	// Limitation du nombre de caractères
	$patient_nom_search    = trim($patient_nom);
  $patient_prenom_search = trim($patient_prenom);
	if ($limit_char_search = CAppUI::conf("dPpatients CPatient limit_char_search")) {
	  $patient_nom_search    = substr($patient_nom_search   , 0, $limit_char_search);
	  $patient_prenom_search = substr($patient_prenom_search, 0, $limit_char_search);
	}
	
	if ($patient_nom_search) {
		$patient_nom_soundex = $soundexObj->build($patient_nom_search);
	  $where[] = "`nom` LIKE '$patient_nom_search%' OR `nom_jeune_fille` LIKE '$patient_nom_search%'";
	  $whereSoundex[] = "`nom_soundex2` LIKE '$patient_nom_soundex%' OR `nomjf_soundex2` LIKE '$patient_nom_soundex%'";
	}
	
	if ($patient_prenom_search) {
    $patient_prenom_soundex = $soundexObj->build($patient_prenom_search);
	  $where["prenom"]                 = "LIKE '$patient_prenom_search%'";
	  $whereSoundex["prenom_soundex2"] = "LIKE '$patient_prenom_soundex%'";
	}
	
  if ($patient_year || $patient_month || $patient_day) {
    $patient_naissance = 
      CValue::first($patient_year , "%") . "-" .
      CValue::first($patient_month, "%") . "-" .
      CValue::first($patient_day  , "%");
    $where["naissance"] = $whereSoundex["naissance"] = "LIKE '$patient_naissance'";
  }
	
	if ($patient_ville) $where["ville"] = $whereSoundex["ville"] = "LIKE '$patient_ville%'";
	if ($patient_cp)    $where["cp"]    = $whereSoundex["cp"]    = "LIKE '$patient_cp%'";
	
	$patients        = array();
	$patientsSoundex = array();
	
	$order = "nom, prenom, naissance";
	$pat = new CPatient();
	
	// Chargement des patients
	if ($where) {
	  $patients = $pat->loadList($where, $order, $showCount);
	}
	
	if ($whereSoundex) {
	  $patientsSoundex = $pat->loadList($whereSoundex, $order, $showCount);
	  $patientsSoundex = array_diff_key($patientsSoundex, $patients);
	}
	
	// Sélection du premier de la liste si aucun n'est déjà sélectionné
	if (!$patient->_id && count($patients) == 1) {
	  $patient = reset($patients);
	}
	
	// Patient vitale associé trouvé : prioritaire
	if ($patVitale->_id) {
	  $patient = $patVitale;
	  // Au cas où il n'aurait pas été trouvé grâce aux champs
	  $patients[$patient->_id] = $patient; 
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dPsanteInstalled", CModule::getInstalled("dPsante400"));
$smarty->assign("canPatients"     , CModule::getCanDo("dPpatients")    );
$smarty->assign("canAdmissions"   , CModule::getCanDo("dPadmissions")  );
$smarty->assign("canPlanningOp"   , CModule::getCanDo("dPplanningOp")  );
$smarty->assign("canCabinet"      , CModule::getCanDo("dPcabinet")     );

$smarty->assign("nom"                 , $patient_nom              );
$smarty->assign("prenom"              , $patient_prenom           );
$smarty->assign("naissance"           , $patient_naissance        );
$smarty->assign("ville"               , $patient_ville            );
$smarty->assign("cp"                  , $patient_cp               );
$smarty->assign("nom_search"          , $patient_nom_search       );
$smarty->assign("prenom_search"       , $patient_prenom_search    );

$smarty->assign("useVitale"           , $useVitale                );
$smarty->assign("patVitale"           , $patVitale                );
$smarty->assign("patients"            , $patients                 );
$smarty->assign("patientsSoundex"     , $patientsSoundex          );

$smarty->assign("patient"             , $patient                  );
$smarty->assign("board"               , 0                         );
$smarty->assign("patient_ipp"         , $patient_ipp              );

$smarty->display("vw_idx_patients.tpl");

?>