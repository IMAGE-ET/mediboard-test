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
$debug        = CValue::get("debug", 0);    //to mbtrace get values

//trace
if ($debug) {
  mbTrace(
    array(
    "ipp"         => $ipp,
    "nda"         => $nda,
    "nom"         => $nom,
    "prenom"      => $prenom,
    "naissance"   => $date_naiss,
    "date_sejour" => $date_sejour,
    "view"        => $view
  )
  );
}

//view list
$mods_available = array(
  "patient"       => "m=patients&a=ajax_vw_patient_complete&patient_id=",
  "soins"         => "m=soins&a=ajax_vw_dossier_soin&sejour_id=",
  "labo"          => "m=Imeds&a=httpreq_vw_sejour_results&sejour_id="
);


//-----------------------------------------------------------------
// VIEW
// view = none
if ($view == "none") {
  CAppUI::stepAjax("context-view_required", UI_MSG_ERROR);
}
//view not registered
if (!array_key_exists($view, $mods_available)) {
  CAppUI::stepAjax("context-view_not-registered", UI_MSG_ERROR);
}



//-----------------------------------------------------------------
//view patient
if ($view == "patient") {
  $nbpatients = 0;
  $patient = new CPatient();
  if ($ipp) {
    $patient->_IPP = $ipp;
    $patient->loadFromIPP();
    if ($patient->_id) {
      $nbpatients = 1;
    }
  }
  //search for a patient
  else {
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

  if ($patient->_id) {
    CAppUI::redirect($mods_available["patient"].$patient->_id);
  }
  //non existing patient
  else {
    if ($patient->_IPP) {
      CAppUI::stepAjax("context-nonexisting-patient-ipp", UI_MSG_ERROR, $patient->_IPP);
    }
    else {
      CAppUI::stepAjax("context-nonexisting-patient", UI_MSG_ERROR);
    }
  }
} //end patient

//-----------------------------------------------------------------
// labo
if (($view == 'labo') ||($view == 'soins')) {
  //nda
  $sejour = new CSejour();
  if ($nda) {
    $sejour->loadFromNDA($nda);
  }
  else {
    CAppUI::stepAjax("context-nda-required", UI_MSG_ERROR, $view);
  }

  if (!$sejour->_id) {
    CAppUI::stepAjax("context-non-existing-sejour-nda", UI_MSG_ERROR, $nda);
  }

  CAppUI::redirect($mods_available[$view].$sejour->_id);
}

