<?php

/**
 * Use for external request and redirect to the right module/view
 *
 * @category Context
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */


CCanDo::checkRead();

//gathering
$ipp          = CValue::get("ipp");
$nda          = CValue::get("nda");
$nom          = trim(CValue::get("name"));
$prenom       = trim(CValue::get("firstname"));
$date_naiss   = CValue::get("birthdate");
$date_sejour  = CValue::get("date_sejour");
$view         = CValue::get("view", "none");
$nbpatients   = 0;


//view list
// PATTERN : MODULE , AJAX, TYPE
$mods_available = array(
  "patient"       => array("dPpatients", "ajax_vw_patient_complete", "patient"),  //dossier patient
  "soins"         => array("soins", "vw_dossier_sejour", "sejour"),             //dossier de soin (complet)
  "labo"          => array("Imeds", "httpreq_vw_sejour_results", "sejour")      //labo result
);


//-----------------------------------------------------------------
// VIEWS
// view = none
if ($view == "none") {
  CAppUI::stepAjax("context-view_required", UI_MSG_ERROR);
}
//view not registered
if (!array_key_exists($view, $mods_available)) {
  CAppUI::stepAjax("context-view_not-registered", UI_MSG_ERROR, $view);
}


//-----------------------------------------------------------------
//PATIENT
$this_module = $mods_available[$view][0];
if (!CModule::getActive($this_module)) {
  CAppUI::stepAjax("context-module%s-not-activated", UI_MSG_ERROR, $this_module);
}

//find a patient
$patient = new CPatient();
//IPP Case
if ($ipp) {
  $patient->_IPP = $ipp;
  $patient->loadFromIPP();
  if ($patient->_id) {
    $nbpatients = 1;
  }
}

//global case
if (!$nbpatients) {
  $where = array();
  if ($nom) {
    $where[] = "`nom` LIKE '$nom%' OR `nom_jeune_fille` LIKE '$nom%'";
  }
  if ($prenom) {
    $where["prenom"] = "LIKE '$prenom%'";
  }
  if ($date_naiss) {
    $where["naissance"] = "LIKE '$date_naiss'";
  }

  $nbPat = $patient->countList($where);
  switch ($nbPat) {
    case 0:
      CAppUI::stepAjax("context-none-patient", UI_MSG_ERROR);
      break;

    case 1:
      $patient->loadObject($where);
      break;

    default:  //more than 1
      CAppUI::stepAjax("context-multiple-patient", UI_MSG_ERROR, $nbPat);
      break;
  }
}


//-----------------------------------------------------------------
//Sejour
$sejour = new CSejour();
if ($mods_available[$view][2] == 'sejour') {

  if ($nda) {
    $sejour->loadFromNDA($nda);
  }
  //patient, with a date = sejour
  elseif ($patient->_id) {
    if (!$date_sejour) {
      CAppUI::stepAjax("context-sejour-patientOK-date-required", UI_MSG_ERROR, $view);
    }

    $date_sejour = mbDateTime($date_sejour);
    $where = array();
    $where[] = "'$date_sejour' BETWEEN entree AND sortie";
    $where["patient_id"] = " = $patient->_id";
    $sejours = $sejour->countList($where);
    switch ($sejours) {
      case 0:
        CAppUI::stepAjax("context-none-sejour", UI_MSG_ERROR);
        break;

      case 1:
        $sejour->loadObject($where);
        break;

      default:
        CAppUI::stepAjax("context-multiple-sejour", UI_MSG_ERROR, $sejours);
        break;
    }
  }
  //something is missing
  else {
    CAppUI::stepAjax("context-nda-or-PatientPlusDate-required", UI_MSG_ERROR, $view);
  }
}


//-----------------------------------------------------------------
if ($mods_available[$view][2] == "patient") {
  if (!$patient->_id) {
    if ($patient->_IPP) {
      CAppUI::stepAjax("context-nonexisting-patient-ipp%s", UI_MSG_ERROR, $patient->_IPP);
    }
    else {
      CAppUI::stepAjax("context-nonexisting-patient", UI_MSG_ERROR);
    }
  }

  $url = formRequest($mods_available[$view])."&patient_id=".$patient->_id;
  CAppUI::redirect($url);
}

//-----------------------------------------------------------------
// labo
if ($mods_available[$view][2] == "sejour") {
  if (!$sejour->_id) {
    CAppUI::stepAjax("context-none-sejour", UI_MSG_ERROR);
  }

  $url = formRequest($mods_available[$view])."&sejour_id=".$sejour->_id;
  CAppUI::redirect($url);
}


//TOOLS
/**
 * Create the redirect string from an array
 *
 * @param array $requete array of ["module", "action", ...]
 *
 * @return string
 */
function formRequest($requete) {
  $redirect = "";
  $redirect .= "m=".$requete[0];
  $redirect .= "&a=".$requete[1];
  if (CValue::get("dialog", 1)) {
    $redirect .= "&dialog=1";
  }
  return $redirect;
}
