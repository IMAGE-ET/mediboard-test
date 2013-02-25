<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Sébastien Fillonneau
*/

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

$op    = CValue::getOrSession("op");
$date  = CValue::getOrSession("date", mbDate());

$consultAnesth  = new CConsultAnesth();
$consult        = new CConsultation();
$userSel        = new CMediusers();
$operation      = new COperation();
$operation->load($op);
$operation->loadRefChir();
$operation->loadRefSejour();
$consult_anesth = $operation->loadRefsConsultAnesth();

if ($consult_anesth->_id) {
  $consult_anesth->loadRefConsultation();
  $consult = $consult_anesth->_ref_consultation;
  $consult->_ref_consult_anesth = $consultAnesth;
  $consult->loadRefPlageConsult();
  $consult->loadRefsDocItems();
  $consult->loadRefPatient();
  $prat_id = $consult->_ref_plageconsult->chir_id;

  $consult_anesth->loadRefs();

  // On charge le praticien
  $userSel->load($prat_id);
  $userSel->loadRefs();
}

$anesth = new CTypeAnesth();
$anesth = $anesth->loadList(null, "name");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("op"             , $op);
$smarty->assign("date"           , $date);
$smarty->assign("operation"      , $operation);
$smarty->assign("anesth"         , $anesth);
$smarty->assign("techniquesComp" , new CTechniqueComp());
$smarty->assign("isPrescriptionInstalled", CModule::getActive("prescription"));

$smarty->display("vw_anesthesie.tpl");
