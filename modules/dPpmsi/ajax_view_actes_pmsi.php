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

// Chargement du séjour et des ses actes
$sejour  = new CSejour();
$sejour->load(CValue::get("sejour_id"));
$sejour->loadRefPatient();
$sejour->loadRefPraticien();
$sejour->loadRefsActes();
$sejour->canDo();
$sejour->countExchanges();
$sejour->loadRefDossierMedical()->loadComplete();
$sejour->_ref_patient->loadRefDossierMedical()->loadComplete();

// Chargement des interventions et de leurs actes
foreach ($sejour->loadRefsOperations() as $_op) {
  $_op->loadRefPatient();
  $_op->loadRefAnesth()->loadRefFunction();
  $_op->loadRefPraticien()->loadRefFunction();
  $_op->loadRefPlageOp();
  $_op->loadRefSalle();
  $_op->loadRefsActes();
  $_op->canDo();
  $_op->countExchanges();
  $_op->loadRefsConsultAnesth()->loadRefConsultation()->loadRefPlageConsult();
}

// Chargement des consultations et de leurs actes
foreach ($sejour->loadRefsConsultations() as $_consult) {
  $_consult->loadRefPatient();
  $_consult->loadRefPraticien()->loadRefFunction();
  $_consult->loadRefsActes();
  $_consult->canDo();
  $_consult->countExchanges();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);

$smarty->display("inc_vw_actes_pmsi.tpl");