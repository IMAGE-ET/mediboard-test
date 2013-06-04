<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkRead();

$facture_id  = CValue::get("facture_id");
$patient_id  = CValue::get("patient_id");

//Patient sélectionné
$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefsCorrespondantsPatient();

$facture = new CFactureCabinet();
$facture->load($facture_id);
$facture->loadRefPatient();
$facture->_ref_patient->loadRefsCorrespondantsPatient();
$facture->loadRefPraticien();
$facture->loadRefAssurance();
$facture->loadRefsObjects();
$facture->loadRefsReglements();
$facture->loadRefsRelances();
$facture->loadRefsNotes();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"       , $patient);
$smarty->assign("facture"       , $facture);

$smarty->display("inc_vw_assurances.tpl");
