<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$operation_id = CValue::getOrSession("operation_id", 0);
if(!$operation_id) {
  $AppUI->setMsg("Vous devez selectionner une intervention", UI_MSG_ERROR);
  $AppUI->redirect("m=dPpmsi&tab=vw_dossier");
}
$selOp = new COperation;
$selOp->load($operation_id);
$selOp->loadRefs();
$selOp->_ref_sejour->loadRefsFwd();
foreach($selOp->_ext_codes_ccam as $key => $value) {
	$selOp->_ext_codes_ccam[$key] = CCodeCCAM::get($value->code, CCodeCCAM::FULL);
}
$selOp->getAssociationCodesActes();
$selOp->loadPossibleActes();
$selOp->_ref_plageop->loadRefsFwd();

// Tableau des timings
$timing["entree_salle"]    = array();
$timing["pose_garrot"]    = array();
$timing["debut_op"]       = array();
$timing["fin_op"]         = array();
$timing["retrait_garrot"] = array();
$timing["sortie_salle"]    = array();
foreach($timing as $key => $value) {
  for($i = -10; $i < 10 && $selOp->$key !== null; $i++) {
    $timing[$key][] = mbTime("$i minutes", $selOp->$key);
  }
}

// Chargement des praticiens

$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes();

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens();

//Initialisation d'un acte NGAP
$acte_ngap = new CActeNGAP();
$acte_ngap->quantite = 1;
$acte_ngap->coefficient = 1;
$acte_ngap->loadListExecutants();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("acte_ngap"  , $acte_ngap      );
$smarty->assign("selOp"      , $selOp          );
$smarty->assign("timing"     , $timing         );
$smarty->assign("listAnesths", $listAnesths    );
$smarty->assign("listChirs"  , $listChirs      );

$smarty->display("edit_actes.tpl");

?>