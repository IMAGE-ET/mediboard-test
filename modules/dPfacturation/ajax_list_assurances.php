<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */
CCanDo::checkEdit();
$facture_id     = CValue::get("facture_id");
$facture_class  = CValue::get("facture_class");
$patient_id     = CValue::get("patient_id");

$order = "date_debut DESC, date_fin DESC";
//Patient sélectionné
$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefsCorrespondantsPatient($order);

$facture = new $facture_class;
$facture->load($facture_id);  
$facture->loadRefPatient();
$facture->_ref_patient->loadRefsCorrespondantsPatient($order);
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