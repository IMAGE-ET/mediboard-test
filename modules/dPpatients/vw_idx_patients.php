<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$mediuser = CMediusers::get();


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

$patient_nom         = trim(CValue::getOrSession("nom"));
$patient_prenom      = trim(CValue::getOrSession("prenom"));
$patient_ville       = CValue::get("ville");
$patient_cp          = CValue::get("cp");
$patient_day         = CValue::getOrSession("Date_Day");
$patient_month       = CValue::getOrSession("Date_Month");
$patient_year        = CValue::getOrSession("Date_Year");
$patient_naissance   = "$patient_year-$patient_month-$patient_day";
$patient_ipp         = CValue::get("patient_ipp");
$patient_nda         = CValue::get("patient_nda");
$useVitale           = CValue::get("useVitale",  CModule::getActive("fse") && CAppUI::pref('VitaleVision') ? 1 : 0);
$prat_id             = CValue::get("prat_id");
$patient_sexe        = CValue::get("sexe");
$useCovercard        = CValue::get("usecovercard",  CModule::getActive("fse") && CModule::getActive("covercard") ? 1 : 0);
$patient_nom_search    = null;
$patient_prenom_search = null;

$patVitale = new CPatient();

// Liste des praticiens
$prats = $mediuser->loadPraticiens();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dPsanteInstalled", CModule::getInstalled("dPsante400"));

$smarty->assign("nom"                 , $patient_nom);
$smarty->assign("prenom"              , $patient_prenom);
$smarty->assign("naissance"           , $patient_naissance);
$smarty->assign("ville"               , $patient_ville);
$smarty->assign("cp"                  , $patient_cp);
$smarty->assign("nom_search"          , $patient_nom_search);
$smarty->assign("prenom_search"       , $patient_prenom_search);
$smarty->assign("covercard"           , CValue::get("covercard", ""));
$smarty->assign("sexe"                , $patient_sexe);
$smarty->assign("prat_id"             , $prat_id);
$smarty->assign("prats"               , $prats);
$smarty->assign("patient"             , $patient);

$smarty->assign("useVitale"           , $useVitale);
$smarty->assign("useCoverCard"        , $useCovercard);
$smarty->assign("patVitale"           , $patVitale);

$smarty->assign("patient"             , $patient);
$smarty->assign("board"               , 0);
$smarty->assign("patient_ipp"         , $patient_ipp);
$smarty->assign("patient_nda"         , $patient_nda);

$smarty->display("vw_idx_patients.tpl");
