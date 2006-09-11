<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPcabinet", "consultation"));
require_once($AppUI->getModuleClass("dPcabinet", "plageconsult"));

if (!$canEdit) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$consult = new CConsultation();
$chir = new CMediusers;
$pat = new CPatient;
$plageConsult = new CPlageconsult();

//Chargement des aides
$consult->loadAides($AppUI->user_id);

// L'utilisateur est-il praticien?
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien()) {
  $chir = $mediuser;
}

// Vérification des droits sur les praticiens
$listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);

$consultation_id = mbGetValueFromGetOrSession("consultation_id");
$plageconsult_id = mbGetValueFromGet("plageconsult_id", null);

if(!$consultation_id) {
  // A t'on fourni une plage de consultation
  if($plageconsult_id){
    $plageConsult->load($plageconsult_id);    
  } else {
    // A t'on fourni l'id du praticien
    if($chir_id = mbGetValueFromGetOrSession("chir_id")) {
      $chir->load($chir_id);
    }

    // A t'on fourni l'id du patient
    if($pat_id = mbGetValueFromGet("pat_id")) {
      $pat->load($pat_id);
    }
  }
} else {
  $consult->load($consultation_id);
  $consult->loadRefs();
  $consult->_ref_plageconsult->loadRefs();

  $chir =& $consult->_ref_plageconsult->_ref_chir;
  $pat  =& $consult->_ref_patient;
}
// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("plageConsult"     , $plageConsult     );
$smarty->assign("consult"           , $consult           );
$smarty->assign("chir"              , $chir              );
$smarty->assign("pat"               , $pat               );
$smarty->assign("listPraticiens"    , $listPraticiens    );

$smarty->display("addedit_planning.tpl");

?>