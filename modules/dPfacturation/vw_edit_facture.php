<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkEdit();
$facture_class  = CValue::getOrSession("facture_class");
$object_id      = CValue::getOrSession("object_id");
$object_class   = CValue::getOrSession("object_class");
$patient_id     = CValue::getOrSession("patient_id");
$numero         = CValue::get("numero");

/* @var CFacture $facture*/
$facture = new $facture_class;
$facture->_sejour_id = $object_id;
$facture->patient_id = $patient_id;
$facture->loadRefPatient()->loadRefsCorrespondantsPatient();

$sejour = new CSejour();
$sejour->load($object_id);
$sejour->loadRefsFactureEtablissement();
$facture->ouverture     = $sejour->_ref_last_facture->ouverture;
$facture->statut_pro    = $sejour->_ref_last_facture->statut_pro;
$facture->numero        = $numero+1;
$facture->praticien_id  = $sejour->praticien_id;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture", $facture);

$smarty->display("vw_edit_facture.tpl");
