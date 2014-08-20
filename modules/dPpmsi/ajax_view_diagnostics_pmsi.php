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

// Chargement du séjour
$sejour  = new CSejour();
$sejour->load(CValue::get("sejour_id"));
$sejour->canDo();
$sejour->loadRefsAffectations();
$sejour->loadRefsOperations();
$sejour->loadRefsConsultAnesth();
$sejour->loadRefDossierMedical();

// Chargement du dossier du patient
$patient = $sejour->loadRefPatient();
$patient->loadRefDossierMedical();


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"         , $patient);
$smarty->assign("sejour"          , $sejour);

$smarty->display("inc_vw_diagnostics_pmsi.tpl");