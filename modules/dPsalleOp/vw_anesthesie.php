<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();
$ds = CSQLDataSource::get("std");

$salle = mbGetValueFromGetOrSession("salle");
$op    = mbGetValueFromGetOrSession("op");
$date  = mbGetValueFromGetOrSession("date", mbDate());

$consultAnesth  = new CConsultAnesth;
$consult        = new CConsultation;
$userSel        = new CMediusers;
$listModelePrat = array();
$listModeleFunc = array();

if($op) {
  $where = array();
  $where["operation_id"] = $ds->prepare("= %", $op);

  if($consultAnesth->loadObject($where)){
    $consultAnesth->loadRefConsultation();
    $consult = $consultAnesth->_ref_consultation;
    $consult->_ref_consult_anesth = $consultAnesth;
    
    $consult->loadRefPlageConsult();
    $consult->loadRefsFilesAndDocs();
    $consult->loadRefPatient();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    $consult->loadAides($prat_id);
    $consult_anesth =& $consult->_ref_consult_anesth;
    $consult_anesth->loadAides($prat_id);
    
    $consult_anesth->loadRefs();
    if($consult_anesth->_ref_operation->operation_id) {
      $consult_anesth->_ref_operation->loadAides($prat_id);
    }
    
    // On charge le praticien
    $userSel->load($prat_id);
    $userSel->loadRefs();

    // Récupération des modèles
    $whereCommon = array();
    $whereCommon["object_id"]    = "IS NULL";
    $whereCommon["object_class"] = "= 'CConsultAnesth'";
    $order = "nom";
    
    // Modèles de l'utilisateur
    if($userSel->user_id){
      $where = $whereCommon;
      $where["chir_id"] = $ds->prepare("= %", $userSel->user_id);
      $listModelePrat = new CCompteRendu;
      $listModelePrat = $listModelePrat->loadlist($where, $order);
    }
    // Modèles de la fonction
    if($userSel->user_id){
      $where = $whereCommon;
      $where["function_id"] = $ds->prepare("= %", $userSel->function_id);
      $listModeleFunc = new CCompteRendu;
      $listModeleFunc = $listModeleFunc->loadlist($where, $order);
    }
  }
}

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null,$orderanesth);

$techniquesComp = new CTechniqueComp();
$techniquesComp->loadAides($userSel->user_id);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("op"             , $op);
$smarty->assign("date"           , $date);
$smarty->assign("consult"        , $consult);
$smarty->assign("consult_anesth" , $consult->_ref_consult_anesth);
$smarty->assign("listModelePrat" , $listModelePrat);
$smarty->assign("listModeleFunc" , $listModeleFunc);
$smarty->assign("noReglement"    , 1);
$smarty->assign("anesth"         , $anesth);
$smarty->assign("techniquesComp" , $techniquesComp);

$smarty->display("vw_anesthesie.tpl");
?>