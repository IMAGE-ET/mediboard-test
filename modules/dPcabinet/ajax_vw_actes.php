<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$consult_id = CValue::get("consult_id");

$consult = new CConsultation();
$consult->load($consult_id);

$consult->loadExtCodesCCAM();
$consult->getAssociationCodesActes();
$consult->loadPossibleActes();

$consult->canDo();

// Chargement des actes NGAP
$consult->loadRefsActesNGAP();

// Initialisation d'un acte NGAP
$acte_ngap = CActeNGAP::createEmptyFor($consult);

// Chargement des règles de codage
$consult->loadRefsCodagesCCAM();
foreach ($consult->_ref_codages_ccam as $_codage) {
  $_codage->loadPraticien()->loadRefFunction();
  $_codage->loadActesCCAM();
  foreach ($_codage->_ref_actes_ccam as $_acte) {
    $_acte->getTarif();
  }
}

$sejour = $consult->loadRefSejour();

if ($sejour->_id) {
  $sejour->loadExtDiagnostics();
  $sejour->loadRefDossierMedical();
  $sejour->loadDiagnosticsAssocies();
}

$listPrats = $listChirs = CConsultation::loadPraticiens(PERM_EDIT);
$listAnesths = CMediusers::get()->loadAnesthesistes();

$smarty = new CSmartyDP();

$smarty->assign("consult"       , $consult);
$smarty->assign("acte_ngap"     , $acte_ngap);
$smarty->assign("listPrats"     , $listPrats);
$smarty->assign("listChirs"     , $listChirs);
$smarty->assign("listAnesths"   , $listAnesths);

$smarty->display("inc_vw_actes.tpl");