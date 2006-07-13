<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcabinet', 'consultAnesth') );

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

set_time_limit( 1800 );

$sql = "SELECT
          plageconsult.date,
          consultation.consultation_id,
          operations.operation_id,
          consultation_anesth.consultation_anesth_id,
          patients.nom,
          patients.prenom
        FROM
          patients,
          consultation,
          plageconsult
        LEFT JOIN operations
        ON operations.pat_id = patients.patient_id
        LEFT JOIN consultation_anesth
        ON consultation_anesth.consultation_id = consultation.consultation_id
        WHERE
          (plageconsult.chir_id = '23'
          OR plageconsult.chir_id = '22'
          OR plageconsult.chir_id = '25'
          OR plageconsult.chir_id = '19')
        AND consultation.plageconsult_id = plageconsult.plageconsult_id
        AND consultation.patient_id = patients.patient_id
        AND operations.operation_id IS NOT NULL
        AND consultation_anesth.consultation_anesth_id IS NULL
        GROUP BY consultation.consultation_id
        ORDER BY plageconsult.date";

$listConsult = db_loadlist($sql);

$n = count($listConsult);
$i = 0;

foreach($listConsult as $key => $value) {
  $consultAnesth = new CConsultAnesth();
  $consultAnesth->consultation_id = $value["consultation_id"];
  $consultAnesth->operation_id = $value["operation_id"];
  if(!$consultAnesth->store())
    $i++;
}

echo "$i consultations d'anesthésie sur un besoin de $n";

?>