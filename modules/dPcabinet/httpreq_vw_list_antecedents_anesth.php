<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$sejour_id = CValue::getOrSession("sejour_id", 0);

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);

// Chargement du dossier medical
$sejour->loadRefDossierMedical();
$dossier_medical =& $sejour->_ref_dossier_medical;

// Chargement des antecedents et traitements
$dossier_medical->loadRefsAntecedents(true);
if ($dossier_medical->_ref_antecedents_by_type) {
  $dossier_medical->countAntecedents();
  $dossier_medical->countTraitements();
  foreach ($dossier_medical->_ref_antecedents_by_type as &$type) {
    foreach ($type as &$ant) {
      $ant->loadLogs();
    }
  }
}

$dossier_medical->loadRefsTraitements(true);

// Chargement de la prescription de sejour
$prescription = $sejour->loadRefPrescriptionSejour();

// Chargement des lignes de tp de la prescription
$lines_tp = array();
if($prescription && $prescription->_id){
  $line_tp = new CPrescriptionLineMedicament();
  $line_tp->prescription_id = $prescription->_id;
  $line_tp->traitement_personnel = 1;
  $lines_tp = $line_tp->loadMatchingList();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("lines_tp", $lines_tp);
$smarty->display("inc_list_ant_anesth.tpl");

?>