<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $can;

$can->needsRead();

$name      = mbGetValueFromGet("name"     );
$firstName = mbGetValueFromGet("firstName");

// Gestion du cas vitale
$patVitale = null;
if (mbGetValueFromGet("useVitale")) {
  $patVitale = new CPatient;
  $patVitale->getValuesFromVitale();
  
  $name = $patVitale->nom; 
  $firstName = $patVitale->prenom; 
}

// Recherche sur valeurs exactes et phonétique
$where        = array();
$whereSoundex = array();
$soundexObj   = new soundex2();

if($name != "" || $firstName != "") {
  $where["nom"]                    = "LIKE '$name%'";
  $where["prenom"]                 = "LIKE '$firstName%'";
  $whereSoundex["nom_soundex2"]    = "LIKE '".$soundexObj->build($name)."%'";
  $whereSoundex["prenom_soundex2"] = "LIKE '".$soundexObj->build($firstName)."%'";
} else {
  $where[]        = "0";
  $whereSoundex[] = "0";
}
$limit = "0, 100";
$order = "patients.nom, patients.prenom";

$pat             = new CPatient();
$patients        = array();
$patientsSoundex = array();

$patients = $pat->loadList($where, $order, $limit);
if ($nbExact = (100 - count($patients))) {
  $limit = "0, $nbExact";
  $patientsSoundex = $pat->loadList($whereSoundex, $order, $limit);
  $patientsSoundex = array_diff_key($patientsSoundex, $patients);
}

// Chargement des consultations du jour
function loadConsultationsDuJour(&$patients) {
  $today = mbDate();
  $where = array();
  $where["plageconsult.date"] = "= '$today'";
  foreach ($patients as &$patient) {
    $patient->loadRefsConsultations($where);
    foreach ($patient->_ref_consultations as &$consult) {
      $consult->loadRefPlageConsult();
    }
  }
  
}

loadConsultationsDuJour($patients);
loadConsultationsDuJour($patientsSoundex);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("name"            , $name            );
$smarty->assign("firstName"       , $firstName       );

$smarty->assign("patVitale"       , $patVitale);

$smarty->assign("patients"        , $patients        );
$smarty->assign("patientsSoundex" , $patientsSoundex );

$smarty->display("pat_selector.tpl");

?>