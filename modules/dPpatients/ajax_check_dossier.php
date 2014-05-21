<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();
$ds   = CSQLDataSource::get("std");
$mode = CValue::get("mode", "check");

$request = "SELECT DISTINCT(d.object_id), d.object_class
            FROM `dossier_medical` d
            WHERE d.`object_id` <> '0'
            AND EXISTS (
              SELECT * FROM `dossier_medical` e
              WHERE e.`object_class` = d.`object_class`
              AND e.`object_id` = d.`object_id`
              AND e.`dossier_medical_id` <> d.`dossier_medical_id`
            );";
$resultats = $ds->loadList($request);
CAppUI::stepAjax("Dossiers à corriger: ".count($resultats), UI_MSG_OK);

if ($mode == "repair") {
  $correction = 0;
  foreach ($resultats as $result) {
    //Dossier de références
    $where = array();
    $where["object_class"] = "= '".$result['object_class']."'";
    $where["object_id"] = "= '".$result['object_id']."'";
    $dossier_ok = new CDossierMedical();
    $dossier_ok->loadObject($where, "dossier_medical_id ASC");
    $dossier_ok->loadRefsAntecedents();
    $dossier_ok->loadRefsEtatsDents();
    $dossier_ok->loadRefsTraitements();

    //Chargement des autres dossiers
    $_dossier = new CDossierMedical();
    $where["dossier_medical_id"] = "!= '".$dossier_ok->_id."'";
    $dossiers = $_dossier->loadList($where);

    //Merge des dossiers
    foreach ($dossiers as $dossier) {
      /* @var CDossierMedical $dossier*/
      if ($msg = $dossier_ok->merge(array($dossier))) {
        mbTrace($msg);
      }
      else {
        $correction++;
      }
    }
  }
  CAppUI::stepAjax("Dossiers corrigés: $correction", UI_MSG_OK);
}