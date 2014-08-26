<?php 

/**
 * $Id$
 *  
 * @category PMSI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadExtDiagnostics();
$sejour->loadRefDossierMedical();
$sejour->loadDiagnosticsAssocies();

$patient = $sejour->loadRefPatient();

$patient->loadRefDossierMedical();

$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->assign("patient", $patient);

$smarty->display("inc_diags_dossier.tpl");