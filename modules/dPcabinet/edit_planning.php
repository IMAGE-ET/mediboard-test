<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

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

$listModelePrat = array();
$listModeleFunc = array();

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
  $canConsult = $consult->canDo();
  
  $canConsult->needsRead("consultation_id");

  $consult->loadRefs();
  $consult->_ref_plageconsult->loadRefs();

  $chir =& $consult->_ref_plageconsult->_ref_chir;
  $pat  =& $consult->_ref_patient;
  
	// Modèles de l'utilisateur
	$order = "nom";
	if ($chir->user_id) {
	  $where = array();
	  $where["object_class"] = "= 'CConsultation'";
	  $where["chir_id"] = "= '$chir->user_id'";
	  $listModelePrat = CCompteRendu::loadModeleByCat(null, $where, $order, true);
	}
	
	// Modèles de la fonction
	if ($chir->user_id) {
	  $where = array();
	  $where["object_class"] = "= 'CConsultation'";
	  $where["function_id"] = "= '$chir->function_id'";
	  $listModeleFunc = CCompteRendu::loadModeleByCat(null, $where, $order, true);
	}
}



// Création du template
$smarty = new CSmartyDP();

$smarty->assign("plageConsult"     	, $plageConsult     );
$smarty->assign("consult"           , $consult           );
$smarty->assign("chir"              , $chir              );
$smarty->assign("pat"               , $pat               );
$smarty->assign("listPraticiens"    , $listPraticiens    );

$smarty->assign("listModelePrat"		, $listModelePrat);
$smarty->assign("listModeleFunc"		, $listModeleFunc);

$smarty->display("addedit_planning.tpl");

?>