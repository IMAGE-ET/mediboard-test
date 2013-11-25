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

// Si le module Tarmed est installé chargement d'un acte
$acte_tarmed = null;
$acte_caisse = null;
if (CModule::getActive("tarmed")) {
  $acte_tarmed = CActeTarmed::createEmptyFor($consult);
  $acte_caisse = CActeCaisse::createEmptyFor($consult);
}

$total_tarmed = $consult->loadRefsActesTarmed();
$total_caisse = $consult->loadRefsActesCaisse();
$soustotal_base = array("tarmed" => $total_tarmed["base"], "caisse" => $total_caisse["base"]);
$soustotal_dh   = array("tarmed" => $total_tarmed["dh"], "caisse" => $total_caisse["dh"]);
$total["tarmed"] = round($total_tarmed["base"]+$total_tarmed["dh"], 2);
$total["caisse"] = round($total_caisse["base"]+$total_caisse["dh"], 2);

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
$smarty->assign("acte_tarmed"   , $acte_tarmed);
$smarty->assign("acte_caisse"   , $acte_caisse);
$smarty->assign("total"         , $total);
$smarty->assign("soustotal_base", $soustotal_base);
$smarty->assign("soustotal_dh"  , $soustotal_dh);
$smarty->assign("listPrats"     , $listPrats);
$smarty->assign("listChirs"     , $listChirs);
$smarty->assign("listAnesths"   , $listAnesths);

$smarty->display("inc_vw_actes.tpl");