<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$operation_id = CValue::getOrSession("operation_id", 0);

$operation = new COperation();
$operation->load($operation_id);
$operation->loadRefs();
$operation->countExchanges();
$operation->isCoded();
$operation->canDo();
$operation->_ref_sejour->loadRefsFwd();
foreach ($operation->_ext_codes_ccam as $key => $value) {
  $operation->_ext_codes_ccam[$key] = CDatedCodeCCAM::get($value->code);
}
$operation->getAssociationCodesActes();
$operation->loadPossibleActes();
$operation->_ref_plageop->loadRefsFwd();
$operation->loadRefPraticien();

if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
  // Chargement des règles de codage
  $operation->loadRefsCodagesCCAM();
  foreach ($operation->_ref_codages_ccam as $_codages_by_prat) {
    foreach ($_codages_by_prat as $_codage) {
      $_codage->loadPraticien()->loadRefFunction();
      $_codage->loadActesCCAM();
      $_codage->getTarifTotal();
      foreach ($_codage->_ref_actes_ccam as $_acte) {
        $_acte->getTarif();
      }
    }
  }
}

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_DENY);

//Initialisation d'un acte NGAP
$acte_ngap = CActeNGAP::createEmptyFor($operation);
// Liste des dents CCAM
$liste_dents = reset(CDentCCAM::loadList());

$user = CMediusers::get();
$user->isPraticien();

// Création du template
$smarty = new CSmartyDP("modules/dPsalleOp");

$smarty->assign("acte_ngap"    , $acte_ngap);
$smarty->assign("liste_dents"  , $liste_dents);
$smarty->assign("subject"      , $operation);
$smarty->assign("listAnesths"  , $listAnesths);
$smarty->assign("listChirs"    , $listChirs);
$smarty->assign('user'         , $user);
$smarty->assign("_is_dentiste" , $operation->_ref_chir->isDentiste());

$smarty->display("inc_codage_actes.tpl");
