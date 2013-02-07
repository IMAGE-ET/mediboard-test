<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpmsi
 * @author     Romain Ollivier <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkEdit();

$operation_id = CValue::getOrSession("operation_id", 0);
if (!$operation_id) {
  CAppUI::setMsg("Vous devez selectionner une intervention", UI_MSG_ERROR);
  CAppUI::redirect("m=dPpmsi&tab=vw_dossier");
}

$selOp = new COperation;
$selOp->load($operation_id);
$selOp->loadRefs();
$selOp->countExchanges();
$selOp->isCoded();
$selOp->canDo();
$selOp->_ref_sejour->loadRefsFwd();
foreach ($selOp->_ext_codes_ccam as $key => $value) {
  $selOp->_ext_codes_ccam[$key] = CCodeCCAM::get($value->code, CCodeCCAM::FULL);
}
$selOp->getAssociationCodesActes();
$selOp->loadPossibleActes();
$selOp->_ref_plageop->loadRefsFwd();

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes();

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens();

//Initialisation d'un acte NGAP
$acte_ngap = CActeNGAP::createEmptyFor($selOp);

$acte_tarmed = null;
if (CModule::getActive("tarmed")) {
  $acte_tarmed = CActeTarmed::createEmptyFor($selOp);
}
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("acte_ngap"  , $acte_ngap  );
$smarty->assign("acte_tarmed", $acte_tarmed);
$smarty->assign("selOp"      , $selOp      );
$smarty->assign("listAnesths", $listAnesths);
$smarty->assign("listChirs"  , $listChirs  );
$smarty->assign("module"     , "dPpmsi"    );

$smarty->display("edit_actes.tpl");

?>