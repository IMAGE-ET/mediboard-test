<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPprescription
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dossier_medical_id = CValue::get("dossier_medical_id");
$sejour_id          = CValue::get("sejour_id");

$dossier_medical = new CDossierMedical;
$dossier_medical->load($dossier_medical_id);

$dossier_medical->loadRefPrescription();
$prescription = $dossier_medical->_ref_prescription;

if (count($prescription->_ref_prescription_lines)) {
  foreach($prescription->_ref_prescription_lines as $_line) {
    $_line->loadRefsPrises();
  }
}

$smarty = new CSmartyDP;

$smarty->assign("dossier_medical", $dossier_medical);
$smarty->assign("sejour_id"      , $sejour_id);
$smarty->display("inc_vw_traitements_personnels.tpl");
?>