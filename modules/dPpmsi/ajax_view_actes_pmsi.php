<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkEdit();

$prat = new CMediusers();
$listPrats = $prat->loadPraticiens();

// Chargement du séjour et des ses actes
$sejour  = new CSejour();
$sejour->load(CValue::get("sejour_id"));
$patient = $sejour->loadRefPatient();
$sejour->loadRefPraticien();
$sejour->loadRefsActes();
$sejour->loadExtCodesCCAM();
$sejour->getAssociationCodesActes();
$sejour->loadPossibleActes();
$sejour->canDo();
$sejour->countExchanges();
$sejour->loadRefDossierMedical()->loadComplete();
$patient->loadRefDossierMedical()->loadComplete();

// Chargement des interventions et de leurs actes
$sejour->loadRefsOperations();
foreach ($sejour->_ref_operations as $_op) {
  $_op->loadRefPatient();
  $_op->loadRefPraticien();
  $_op->loadRefsActes();
  $_op->loadExtCodesCCAM();
  $_op->getAssociationCodesActes();
  $_op->loadPossibleActes();
  $_op->canDo();
  $_op->countExchanges();
  $_op->loadRefsConsultAnesth()->loadRefConsultation()->loadRefPlageConsult();
}

// Chargement des consultations et de leurs actes
$sejour->loadRefsConsultations();
foreach ($sejour->_ref_consultations as $_consult) {
  $_consult->loadRefPatient();
  $_consult->loadRefPraticien();
  $_consult->loadRefsActes();
  $_consult->loadExtCodesCCAM();
  $_consult->getAssociationCodesActes();
  $_consult->loadPossibleActes();
  $_consult->canDo();
  $_consult->countExchanges();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listPrats", $listPrats);
$smarty->assign("sejour"   , $sejour);

$smarty->display("inc_vw_actes_pmsi.tpl");