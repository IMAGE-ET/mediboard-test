<?php /* $Id: vw_recherche.php 8520 2010-04-09 14:27:59Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 8520 $
* @author Alexis Granger
*/

$user_id = CValue::getOrSession("user_id", CAppUI::$user->_id);

// save form info
$patient = new CPatient;
bindHashToObject($_GET + $_SESSION["dPpatients"], $patient);
$patient->loadRefsFwd();

$consult = new CConsultation;
bindHashToObject($_GET + $_SESSION["dPpatients"], $consult);
$consult->loadRefsFwd();
$consult->_rques_consult = CValue::get("_rques_consult", CValue::session("_rques_consult"));
$consult->_examen_consult = CValue::get("_examen_consult", CValue::session("_examen_consult"));

$sejour = new CSejour;
bindHashToObject($_GET + $_SESSION["dPpatients"], $sejour);
$sejour->loadRefsFwd();
$sejour->_rques_sejour = CValue::get("_rques_sejour", CValue::session("_rques_sejour"));

$interv = new COperation;
bindHashToObject($_GET + $_SESSION["dPpatients"], $interv);
$interv->loadRefsFwd();
$interv->_libelle_interv = CValue::get("_libelle_interv", CValue::session("_libelle_interv"));
$interv->_rques_interv = CValue::get("_rques_interv", CValue::session("_rques_interv"));

$antecedent = new CAntecedent;
bindHashToObject($_GET + $_SESSION["dPpatients"], $antecedent);
$antecedent->loadRefsFwd();

$traitement = new CTraitement;
bindHashToObject($_GET + $_SESSION["dPpatients"], $traitement);
$traitement->loadRefsFwd();

$user = new CMediusers;
$user->load($user_id);

$users_list = array();

if (!CAppUI::$user->isPraticien()) {
  $users_list = $user->loadPraticiens(PERM_READ);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("users_list", $users_list);
$smarty->assign("user_id", $user_id);
$smarty->assign("patient", $patient);
$smarty->assign("antecedent", $antecedent);
$smarty->assign("traitement", $traitement);
$smarty->assign("consult", $consult);
$smarty->assign("sejour", $sejour);
$smarty->assign("interv", $interv);
$smarty->display("vw_recherche_dossier_clinique.tpl");
