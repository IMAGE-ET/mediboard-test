<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$operation_id = mbGetValueFromGetOrSession("operation_id");
$date         = mbGetValueFromGetOrSession("date", mbDate());
$date_now     = mbDate();
$modif_operation = $date>=$date_now;

$operation = new COperation();
$prescription = new CPrescription();
$protocoles = array();
$anesth_id = "";

if($operation_id){
  $operation->load($operation_id);
  $operation->loadRefs();
  
  // Tableau des timings
  $timing["entree_salle"]    = array();
  $timing["pose_garrot"]     = array();
  $timing["debut_op"]        = array();
  $timing["fin_op"]          = array();
  $timing["retrait_garrot"]  = array();
  $timing["sortie_salle"]    = array();
  $timing["induction_debut"] = array();
  $timing["induction_fin"]   = array();
  foreach($timing as $key => $value) {
    for($i = -10; $i < 10 && $operation->$key !== null; $i++) {
      $timing[$key][] = mbTime("$i minutes", $operation->$key);
    }
  }
  // Chargement de la prescription de sejour
	$prescription->object_id = $operation->sejour_id;
	$prescription->object_class = "CSejour";
	$prescription->type = "sejour";
	$prescription->loadMatchingObject();
	
	$anesth_id = ($operation->anesth_id) ? $operation->anesth_id : $operation->_ref_plageop->anesth_id;
	if($anesth_id && CModule::$active['dPprescription']){
	  $protocoles = CPrescription::loadAllProtocolesFor($anesth_id, null, null, 'CSejour');
	}
}

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);

$listAnesthType = new CTypeAnesth;
$orderanesth = "name";
$listAnesthType = $listAnesthType->loadList(null,$orderanesth);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listAnesthType"  , $listAnesthType  );
$smarty->assign("listAnesths"     , $listAnesths     );
$smarty->assign("listChirs"       , $listChirs       );
$smarty->assign("selOp"           , $operation       );
$smarty->assign("date"            , $date            );
$smarty->assign("modif_operation" , $modif_operation );
$smarty->assign("timing"          , $timing          );
$smarty->assign("prescription"    , $prescription    );
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("protocoles"      , $protocoles);
$smarty->assign("anesth_id"       , $anesth_id);
$smarty->display("inc_vw_anesth.tpl");

?>