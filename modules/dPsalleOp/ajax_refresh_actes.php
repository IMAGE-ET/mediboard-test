<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPsalleOp
 * @author     Romain Ollivier <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
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
foreach ($operation->_ext_codes_ccam as $key => $value) {
  $operation->_ext_codes_ccam[$key] = CCodeCCAM::get($value->code, CCodeCCAM::FULL);
}
$operation->getAssociationCodesActes();
$operation->loadPossibleActes();
$operation->_ref_plageop->loadRefsFwd();
$operation->loadRefPraticien();

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_DENY);

//Initialisation d'un acte NGAP
$acte_ngap = CActeNGAP::createEmptyFor($operation);

$acte_tarmed = null;
if (CModule::getActive("tarmed")) {
  $acte_tarmed = CActeTarmed::createEmptyFor($operation);
}
// Création du template
$smarty = new CSmartyDP("modules/dPsalleOp");
$smarty->assign("acte_ngap"  , $acte_ngap      );
$smarty->assign("acte_tarmed", $acte_tarmed    );
$smarty->assign("subject"    , $operation      );
$smarty->assign("listAnesths", $listAnesths    );
$smarty->assign("listChirs"  , $listChirs      );
$smarty->assign("module"     , $module      );
$smarty->assign("m"          , $module      );
$smarty->assign("_is_dentiste" , $operation->_ref_chir->isDentiste());

$smarty->display("inc_codage_actes.tpl");

?>