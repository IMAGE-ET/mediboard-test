<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkAdmin();
$ds   = CSQLDataSource::get("std");
$mode = CValue::get("mode", "check");

$request = "SELECT DISTINCT(d.object_id)
            FROM `dossier_medical` d
            WHERE d.`object_class` = 'CPatient'
            AND d.`object_id` <> '0'
            AND EXISTS (
              SELECT * FROM `dossier_medical` e
              WHERE e.`object_class` = 'CPatient'
              AND e.`object_id` = d.`object_id`
              AND e.`dossier_medical_id` <> d.`dossier_medical_id`
            );";
$resultats = $ds->loadList($request);
CAppUI::stepAjax("Dossiers à corriger: ".count($resultats), UI_MSG_OK);

if ($mode == "repair") {
  $correction = 0;
  foreach ($resultats as $result) {
    $dossier = new CDossierMedical();
    $dossier->object_class = 'CPatient';
    $dossier->object_id = $result['object_id'];
    $dossiers = $dossier->loadMatchingList();
    //mbTrace($result['object_id']);
    $num = 0;
    $dossier_ok = new CDossierMedical();
    foreach ($dossiers as $dossier_cur) {
      //mbTrace($dossier_cur);
      if ($num == 0) {
        $dossier_ok = $dossier_cur;
      }
      else {
        if (!$dossier_ok->codes_cim && $dossier_cur->codes_cim) {
          $dossier_ok->codes_cim = $dossier_cur->codes_cim;
        }
        if (!$dossier_ok->risque_thrombo_patient == 'NR' && $dossier_cur->risque_thrombo_patient != 'NR') {
          $dossier_ok->risque_thrombo_patient = $dossier_cur->risque_thrombo_patient;
        }
        if (!$dossier_ok->risque_MCJ_patient == 'NR' && $dossier_cur->risque_MCJ_patient != 'NR') {
          $dossier_ok->risque_MCJ_patient = $dossier_cur->risque_MCJ_patient;
        }
        if (!$dossier_ok->risque_thrombo_chirurgie == 'NR' && $dossier_cur->risque_thrombo_chirurgie != 'NR') {
          $dossier_ok->risque_thrombo_chirurgie = $dossier_cur->risque_thrombo_chirurgie;
        }
        if (!$dossier_ok->risque_antibioprophylaxie == 'NR' && $dossier_cur->risque_antibioprophylaxie != 'NR') {
          $dossier_ok->risque_antibioprophylaxie = $dossier_cur->risque_antibioprophylaxie;
        }
        if (!$dossier_ok->risque_prophylaxie == 'NR' && $dossier_cur->risque_prophylaxie != 'NR') {
          $dossier_ok->risque_prophylaxie = $dossier_cur->risque_prophylaxie;
        }
        if (!$dossier_ok->risque_MCJ_chirurgie == 'NR' && $dossier_cur->risque_MCJ_chirurgie != 'NR') {
          $dossier_ok->risque_MCJ_chirurgie = $dossier_cur->risque_MCJ_chirurgie;
        }
        if (!$dossier_ok->facteurs_risque && $dossier_cur->facteurs_risque) {
          $dossier_ok->facteurs_risque = $dossier_cur->facteurs_risque;
        }
        if (!$dossier_ok->absence_traitement && $dossier_cur->absence_traitement) {
          $dossier_ok->absence_traitement = $dossier_cur->absence_traitement;
        }
        if (!$dossier_ok->groupe_sanguin == '?' && $dossier_cur->groupe_sanguin != '?') {
          $dossier_ok->groupe_sanguin = $dossier_cur->groupe_sanguin;
        }
        if (!$dossier_ok->rhesus == '?' && $dossier_cur->rhesus != '?') {
          $dossier_ok->rhesus = $dossier_cur->rhesus  ;
        }
        if (!$dossier_ok->groupe_ok && $dossier_cur->groupe_ok) {
          $dossier_ok->groupe_ok = $dossier_cur->groupe_ok;
        }

        if ($msg = $dossier_cur->delete()) {
          CAppUI::stepAjax($msg, UI_MSG_WARNING);
        }
        else {
          $correction++;
        }
      }
      $num++;
    }
    if ($msg = $dossier_ok->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
  }
  CAppUI::stepAjax("Dossiers corrigé: $correction", UI_MSG_OK);
}