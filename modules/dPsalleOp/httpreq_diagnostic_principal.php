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

$sejour_id  = CValue::getOrSession("sejour_id");
$consult_id = CValue::getOrSession("consult_id");
$modeDAS    = CValue::getOrSession("modeDAS", 1);

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadExtDiagnostics();
$sejour->loadRefDossierMedical();
$sejour->loadDiagnosticsAssocies();

$consult = new CConsultation();
$consult->load($consult_id);
$consult->countActes();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour" , $sejour);
$smarty->assign("modeDAS", $modeDAS);
$smarty->assign("consult", $consult);

$smarty->display("inc_diagnostic_principal.tpl");
