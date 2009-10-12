<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain OLLIVIER
*/

/// @todo: Ce fichier ressemble beaucoup à vw_idx_patient.php, il faudrait factoriser

global $can, $g;
$can->needsRead();

$board = mbGetValueFromGet("board", 0);

$patient_id = mbGetValueFromGetOrSession("patient_id");

// Patients
$patient_nom       = mbGetValueFromGetOrSession("nom"       , "");
$patient_prenom    = mbGetValueFromGetOrSession("prenom"    , "");
$patient_jeuneFille= mbGetValueFromGetOrSession("jeuneFille", "");

$patient_useNaissance = mbGetValueFromGetOrSession("useNaissance" , "off");
$patient_ville     = mbGetValueFromGetOrSession("ville"     , "");
$patient_cp        = mbGetValueFromGetOrSession("cp"        , "");
$patient_day       = mbGetValueFromGetOrSession("Date_Day"  , "");
$patient_month     = mbGetValueFromGetOrSession("Date_Month", "");
$patient_year      = mbGetValueFromGetOrSession("Date_Year" , "");
$patient_ipp       = mbGetValueFromGet("patient_ipp");
$patient_naissance = null;
$useVitale         = mbGetValueFromGet("useVitale");

$patVitale = new CPatient;

$patient = new CPatient;
if ($new = mbGetValueFromGet("new")) {
  $patient->load(null);
  mbSetValueToSession("patient_id", null);
  mbSetValueToSession("selClass", null);
  mbSetValueToSession("selKey", null);
} else {
  $patient->load($patient_id);
}

// Champs vitale
if ($useVitale) {
  $patVitale->getValuesFromVitale();
  $patVitale->updateFormFields();
  $patient_nom    = $patVitale->nom;
  $patient_prenom = $patVitale->prenom;
  mbSetValueToSession("nom", $patVitale->nom);
  mbSetValueToSession("prenom", $patVitale->prenom);
  $patient_useNaissance = "on";
  mbSetValueToSession("useNaissance", "on");
  $patVitale->loadFromIdVitale();
}


// Recherhche par IPP
if($patient_ipp && !$useVitale && CModule::getInstalled("dPsante400")){
  // Initialisation dans le cas d'une recherche par IPP
  $patients = array();
  $patientsSoundex = array();
  $patientsCount = 0;
  $patientsSoundexCount = 0;
  
  $idsante = new CIdSante400();
  $idsante->tag = str_replace('$g', $g, CAppUI::conf("dPpatients CPatient tag_ipp"));
  $idsante->id400 = $patient_ipp;
  $idsante->object_class = "CPatient";
  $idsante->loadMatchingObject();
  
  if($idsante->object_id){
   $patient = new CPatient();
   $patient->load($idsante->object_id);
   $patients[$patient->_id] = $patient; 
  }
} else {
	$where        = array();
	$whereSoundex = array();
	$soundexObj   = new soundex2();
	
	if ($patient_nom = trim($patient_nom)) {
	  $where["nom"]                 = "LIKE '$patient_nom%'";
	  $whereSoundex["nom_soundex2"] = "LIKE '".$soundexObj->build($patient_nom)."%'";
	}
	if ($patient_prenom = trim($patient_prenom)) {
	  $where["prenom"]                 = "LIKE '$patient_prenom%'";
	  $whereSoundex["prenom_soundex2"] = "LIKE '".$soundexObj->build($patient_prenom)."%'";
	}
	if ($patient_jeuneFille = trim($patient_jeuneFille)) {
	  $where["nom_jeune_fille"]        = "LIKE '$patient_jeuneFille%'";
	  $whereSoundex["nomjf_soundex2"]  = "LIKE '".$soundexObj->build($patient_jeuneFille)."%'";
	}
	
	if($patient_year || $patient_month || $patient_day){
		$patient_useNaissance = "on";
	}
	
  if ($patient_useNaissance == "on"){
    if($patient_year || $patient_month || $patient_day){
      $year  = $patient_year  ? "$patient_year-"  : "%-";
      $month = $patient_month ? "$patient_month-" : "%-";
      $day   = $patient_day   ? "$patient_day"    : "%";
      if ($day != "%") {
        $day = str_pad($day, 2, "0", STR_PAD_LEFT);
      }
      
      $patient_naissance = $year.$month.$day;
    
      $where["naissance"] = $whereSoundex["naissance"] = "LIKE '$patient_naissance'";
    }
  }
	
  if ($patient_ville) $where["ville"] = $whereSoundex["ville"] = "LIKE '$patient_ville%'";
  if ($patient_cp)    $where["cp"]    = $whereSoundex["cp"]    = "= '$patient_cp'";
	
	$patients        = array();
	$patientsSoundex = array();

	$pat = new CPatient();
	if ($where) {
	  $patients = $pat->loadList($where, "nom, prenom, naissance", "0, 100");
	}
	if($whereSoundex && ($nbExact = (100 - count($patients)))) {
	  $patientsSoundex = $pat->loadList($whereSoundex, "nom, prenom, naissance", "0, $nbExact");
	  $patientsSoundex = array_diff_key($patientsSoundex, $patients);
	}
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dPsanteInstalled", CModule::getInstalled("dPsante400"));
$smarty->assign("patient_ipp"    , $patient_ipp                );
$smarty->assign("board"          , $board                      );

$smarty->assign("nom"            , $patient_nom                );
$smarty->assign("prenom"         , $patient_prenom             );
$smarty->assign("jeuneFille"     , $patient_jeuneFille         );
$smarty->assign("useNaissance"   , $patient_useNaissance       );
$smarty->assign("ville"          , $patient_ville              );
$smarty->assign("cp"             , $patient_cp                 );
$smarty->assign("naissance"      , $patient_naissance          );

$smarty->assign("useVitale"      , $useVitale                  );
$smarty->assign("patVitale"      , $patVitale                  );
$smarty->assign("patients"       , $patients                   );
$smarty->assign("patientsCount"  , count($patients)            );
$smarty->assign("patientsSoundexCount", count($patientsSoundex));
$smarty->assign("patientsSoundex", $patientsSoundex            );
$smarty->assign("patient"        , $patient                    );

$smarty->display("inc_list_patient.tpl");