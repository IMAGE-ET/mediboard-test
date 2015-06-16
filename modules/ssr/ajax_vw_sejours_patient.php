<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$sejour_id = CValue::getOrSession("sejour_id");

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);

$prescription = $sejour->loadRefPrescriptionSejour();

// Prescription peut ne pas être actif
if ($prescription) {
  $prescription->countBackRefs("prescription_line_element");
}

// Recherche des sejours SSR du patient
$where = array();
$where["patient_id"] = " = '$sejour->patient_id'";
$where["type"] = " = 'ssr'";
$where["annule"] = " = '0'";
$where["sejour_id"] = " != '$sejour->_id'";
$where["sortie"] = " <= '$sejour->entree'";

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where);
foreach ($sejours as $_sejour) {
  $_sejour->loadRefBilanSSR()->loadRefPraticienDemandeur();
  $_sejour->loadRefPraticien(1);

  $prescription = $_sejour->loadRefPrescriptionSejour();
  $prescription->loadRefsLinesElementByCat();
  foreach ($prescription->_ref_prescription_lines_element_by_cat as $_lines) {
    foreach ($_lines as $_line) {
      /* @var CPrescriptionLineElement $_line*/
      $_line->getRecentModification();
    }
  }
}

$colors = CColorLibelleSejour::loadAllFor(CMbArray::pluck($sejours, "libelle"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour"  , $sejour);
$smarty->assign("sejours" , $sejours);
$smarty->assign("colors"  , $colors);
$smarty->display("inc_vw_sejours_patient.tpl");
