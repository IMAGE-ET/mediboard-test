<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$do_merge = CValue::get("do_merge");

$ds = CSQLDataSource::get("std");

$res = $ds->query("SELECT COUNT(*) AS total,
  CONVERT( GROUP_CONCAT(`patient_id` SEPARATOR '|') USING latin1 ) AS ids , 
  LOWER( CONCAT_WS( '-', 
    REPLACE( REPLACE( REPLACE( REPLACE( `nom` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) , 
    REPLACE( REPLACE( REPLACE( REPLACE( `prenom` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) , 
    `naissance`  
    , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `nom_jeune_fille` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
    , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `prenom_2` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
    , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `prenom_3` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
    , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `prenom_4` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
  )) AS hash
  FROM `patients`
  GROUP BY hash
  HAVING total > 1");

CAppUI::stepAjax(intval($ds->numRows($res))." patients identiques :");

if (!$do_merge) {
  $patient = new CPatient();
  while($l = $ds->fetchAssoc($res)){
    $patient_ids = explode("|", $l["ids"]);
    $patient->load(reset($patient_ids));
    CAppUI::stepAjax("$patient->_view (x".count($patient_ids).")");
  }
}
else {
  while($l = $ds->fetchAssoc($res)){
    $patient_ids = explode("|", $l["ids"]);
    
    $patients = array();
    foreach($patient_ids as $id) {
      $p = new CPatient;
      $p->load($id);
      $patients[$id] = $p;
    }
    
    $first_patient = array_shift($patients);
    $first_patient_id = $first_patient->_id;
    
    foreach($patients as $_patient) {
      $patients_array = array($_patient);
      if ($msg = $first_patient->mergeDBFields($patients_array)) {
        CAppUI::stepAjax("$_patient : $msg", UI_MSG_WARNING);
        continue;
      }
      
      /** @todo mergeDBfields resets the _id */
      $first_patient->_id = $first_patient_id;
      
      $first_patient->_merging = $patients_array;
      if ($msg = $first_patient->merge($patients_array)) {
        CAppUI::stepAjax("$_patient : $msg", UI_MSG_WARNING);
      }
    }
    
    if (!$msg) CAppUI::stepAjax("Patient $first_patient fusionné");
  }
}