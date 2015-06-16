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

$user_id = CValue::getOrSession("user_id", CAppUI::$user->_id);

// save form info
$patient = new CPatient();
bindHashToObject($_GET + $_SESSION["dPpatients"], $patient);
$patient->_id = "";
$patient->loadRefsFwd();

$consult = new CConsultation();
bindHashToObject($_GET + $_SESSION["dPpatients"], $consult);
$consult->loadRefsFwd();
$consult->_rques_consult = CValue::get("_rques_consult", CValue::session("_rques_consult"));
$consult->_examen_consult = CValue::get("_examen_consult", CValue::session("_examen_consult"));

$sejour = new CSejour();
bindHashToObject($_GET + $_SESSION["dPpatients"], $sejour);
$sejour->loadRefsFwd();
$sejour->_rques_sejour = CValue::get("_rques_sejour", CValue::session("_rques_sejour"));

$interv = new COperation();
bindHashToObject($_GET + $_SESSION["dPpatients"], $interv);
$interv->loadRefsFwd();
$interv->_libelle_interv = CValue::get("_libelle_interv", CValue::session("_libelle_interv"));
$interv->_rques_interv = CValue::get("_rques_interv", CValue::session("_rques_interv"));

$antecedent = new CAntecedent();
bindHashToObject($_GET + $_SESSION["dPpatients"], $antecedent);
$antecedent->loadRefsFwd();

$traitement = new CTraitement();
bindHashToObject($_GET + $_SESSION["dPpatients"], $traitement);
$traitement->loadRefsFwd();

$prescription = new CPrescription();
$prescription->type = CValue::getOrSession("type_prescription");

$line_med = new CPrescriptionLineMedicament();
$line_med->code_ucd = CValue::getOrSession("code_ucd");
$line_med->code_cis = CValue::getOrSession("code_cis");
$line_med->_ucd_view = CValue::getOrSession("produit");

$libelle_produit = CValue::getOrSession("libelle_produit");

$classes_atc  = CValue::getOrSession("classes_atc");
$keywords_atc = CValue::getOrSession("keywords_atc");

$composant    = CValue::getOrSession("composant");
$keywords_composant = CValue::getOrSession("keywords_composant");

$indication = CValue::getOrSession("indication");
$type_indication = CValue::getOrSession("type_indication");
$keywords_indication = CValue::getOrSession("keywords_indication");

$commentaire = CValue::getOrSession("commentaire");

$user = new CMediusers();
$user->load($user_id);

$users_list = array();

if (!CAppUI::$user->isPraticien()) {
  $users_list = $user->loadPraticiens(PERM_READ);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("users_list"         , $users_list);
$smarty->assign("user_id"            , $user_id);
$smarty->assign("patient"            , $patient);
$smarty->assign("antecedent"         , $antecedent);
$smarty->assign("traitement"         , $traitement);
$smarty->assign("consult"            , $consult);
$smarty->assign("sejour"             , $sejour);
$smarty->assign("interv"             , $interv);
$smarty->assign("prescription"       , $prescription);
$smarty->assign("line_med"           , $line_med);
$smarty->assign("libelle_produit"    , $libelle_produit);
$smarty->assign("classes_atc"        , $classes_atc);
$smarty->assign("keywords_atc"       , $classes_atc);
$smarty->assign("composant"          , $composant);
$smarty->assign("keywords_composant" , $keywords_composant);
$smarty->assign("indication"         , $indication);
$smarty->assign("keywords_indication", $keywords_indication);
$smarty->assign("type_indication"    , $type_indication);
$smarty->assign("commentaire"        , $commentaire);

$smarty->display("vw_recherche_dossier_clinique.tpl");
