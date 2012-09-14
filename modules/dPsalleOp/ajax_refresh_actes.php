<?php /* $Id: edit_actes.php 12659 2011-07-15 14:27:53Z lryo $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: 12659 $
* @author Romain Ollivier
*/

$module       = CValue::getOrSession("module", "dPsalleOp");
$operation_id = CValue::getOrSession("operation_id", 0);

$operation = new COperation;
$operation->load($operation_id);
$operation->loadRefs();
$operation->countExchanges();
$operation->isCoded();
$operation->canDo();
$operation->_ref_sejour->loadRefsFwd();
foreach($operation->_ext_codes_ccam as $key => $value) {
  $operation->_ext_codes_ccam[$key] = CCodeCCAM::get($value->code, CCodeCCAM::FULL);
}
$operation->getAssociationCodesActes();
$operation->loadPossibleActes();
$operation->_ref_plageop->loadRefsFwd();

// Chargement des praticiens

$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_DENY);

//Initialisation d'un acte NGAP
$acte_ngap = new CActeNGAP();
$acte_ngap->quantite = 1;
$acte_ngap->coefficient = 1;
$acte_ngap->loadListExecutants();

$acte_tarmed = null;
if(CModule::getActive("tarmed")){
	//Initialisation d'un acte Tarmed
	$acte_tarmed = new CActeTarmed();
	$acte_tarmed->quantite = 1;
	$acte_tarmed->loadListExecutants();
	$acte_tarmed->loadRefExecutant();
}
// Création du template
$smarty = new CSmartyDP("modules/dPsalleOp");
$smarty->assign("acte_ngap"  , $acte_ngap      );
$smarty->assign("acte_tarmed", $acte_tarmed      );
$smarty->assign("subject"    , $operation      );
$smarty->assign("listAnesths", $listAnesths    );
$smarty->assign("listChirs"  , $listChirs      );
$smarty->assign("module"     , $module      );
$smarty->assign("m"          , $module      );

$smarty->display("inc_codage_actes.tpl");

?>