<?php

/**
 * Modification de naissance
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$naissance_id   = CValue::get("naissance_id");
$sejour_id      = CValue::get("sejour_id");
$operation_id   = CValue::get("operation_id");
$provisoire     = CValue::get("provisoire", 0);
$sejour_id      = CValue::get("sejour_id");
$callback       = CValue::get("callback");

$constantes = new CConstantesMedicales();

$patient    = new CPatient();
$patient->naissance = CMbDT::date();

$sejour = new CSejour();
$sejour->load($sejour_id);
$parturiente = $sejour->loadRefPatient();

$anonmymous = $parturiente ? is_numeric($parturiente->nom) : false;

$naissance  = new CNaissance;

if ($naissance_id) {
  $naissance->load($naissance_id);
  
  // Quand la naissance existe, le praticien à modifier est
  // celui du séjour de l'enfant.
  $sejour = $naissance->loadRefSejourEnfant();
  $patient = $sejour->loadRefPatient();
  $constantes = $patient->getFirstConstantes();
  
  // Heure courante sur la naissance et date courante sur le patient
  // pour transformer le dossier provisoire en naissance
  if (!$naissance->date_time) {
    $naissance->date_time = CMbDT::dateTime();
    $patient->naissance = CMbDT::date();
  }
  
}
else {
  if (!$provisoire) {
    $grossesse = $sejour->loadRefGrossesse();
    $naissance->rang = $grossesse->countBackRefs("naissances") + 1;
    $naissance->date_time = CMbDT::dateTime();
  }
  
  $naissance->sejour_maman_id = $sejour_id;
  $naissance->operation_id = $operation_id;

  // guess cesarienne
  $op = new COperation();
  $op->load($operation_id);
  $bloc = $op->loadRefSalle()->loadRefBloc();
  if ($bloc->_id && $bloc->type != "obst") {
    $naissance->by_caesarean = "1";
  }

  $num_naissance = CAppUI::conf("maternite CNaissance num_naissance") != 1 ?
    CAppUI::conf("maternite CNaissance num_naissance") :
    CAppUI::conf("maternite CNaissance num_naissance", "CGroups-".$sejour->group_id);
  $naissance->num_naissance = $num_naissance + CNaissance::countNaissances();

  if (!$anonmymous) {
    $patient->nom = $parturiente->nom;
  }
}



$sejour->loadRefPraticien();

$smarty = new CSmartyDP();
$smarty->assign("naissance"       , $naissance);
$smarty->assign("patient"         , $patient);
$smarty->assign("constantes"      , $constantes);
$smarty->assign("parturiente"     , $parturiente);
$smarty->assign("provisoire"      , $provisoire);
$smarty->assign("sejour_id"       , $sejour_id);
$smarty->assign("callback"        , $callback);
$smarty->assign("sejour"          , $sejour);
$smarty->assign("operation_id"    , $operation_id);
$smarty->assign("list_constantes" , CConstantesMedicales::$list_constantes);
$smarty->display("inc_edit_naissance.tpl");
