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

$object_class    = CValue::getOrSession("object_class");
$object_id       = CValue::getOrSession("object_id");
$module          = CValue::getOrSession("module");
$do_subject_aed  = CValue::getOrSession("do_subject_aed");
$chir_id         = CValue::getOrSession("chir_id");

$date  = CValue::getOrSession("date", CMbDT::date());

// Chargement de la liste des praticiens
$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_DENY);

// Chargement de la liste des anesthesistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

// Liste des dents CCAM
$liste_dents = reset(CDentCCAM::loadList());

/** @var CCodable $codable */
$codable = new $object_class;
$codable->load($object_id);
$codable->isCoded();

$codable->countActes();
$codable->loadRefPatient();
$codable->loadRefPraticien();
$codable->loadExtCodesCCAM();
$codable->getAssociationCodesActes();
$codable->loadPossibleActes();
$codable->canDo();
if ($codable->_class == "COperation") {
  $codable->countExchanges();
}

if ($codable->_class == "CConsultation") {
  $codable->loadRefSejour()->loadDiagnosticsAssocies();
}

if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
  $codable->loadRefsCodagesCCAM();
  foreach ($codable->_ref_codages_ccam as $_codages_by_prat) {
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

$user = CMediusers::get();
$user->isPraticien();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listAnesths"      , $listAnesths);
$smarty->assign("listChirs"        , $listChirs);
$smarty->assign("liste_dents"      , $liste_dents);
$smarty->assign("subject"          , $codable);
$smarty->assign("module"           , $module);
$smarty->assign("do_subject_aed"   , $do_subject_aed);
$smarty->assign("chir_id"          , $chir_id);
$smarty->assign('user'         , $user);
$smarty->display("inc_codage_ccam.tpl");
