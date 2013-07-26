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

$pat_id = CValue::getOrSession("patSel");

// Liste des Praticiens
$listPrat = CConsultation::loadPraticiens(PERM_READ);

$patient = new CPatient;
$patient->load($pat_id);

// Chargement des références du patient
if ($pat_id) {
  // Infos patient complètes (tableau de droite)
  $patient->loadDossierComplet();
  /*
  foreach ($patient->_ref_consultations as $key => $value) {
    if (!array_key_exists($value->_ref_plageconsult->chir_id, $listPrat)) {
        unset($patient->_ref_consultations[$key]);
    }
  }

  foreach ($patient->_ref_sejours as $key => $sejour) {
    if (!array_key_exists($sejour->praticien_id, $listPrat)) {
      unset($patient->_ref_sejours[$key]);
    } else {
      $patient->_ref_sejours[$key]->loadRefsFwd();
      $patient->_ref_sejours[$key]->loadRefsOperations();
      foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
        if (!array_key_exists($op->chir_id, $listPrat)) {
          unset($patient->_ref_sejours[$key]->_ref_operations[$keyOp]);
        } else {
          //$patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
          $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefPlageOp();
          $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefChir();
          $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->countDocItems();
        }
      }
    }
  }
  */
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canPatients"  , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions", CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"   , CModule::getCanDo("dPcabinet"));

$smarty->assign("patient"    , $patient    );
$smarty->assign("listPrat"   , $listPrat   );

$smarty->display("vw_dossier.tpl");
