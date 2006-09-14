<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

set_time_limit( 1800 );

$sql = "SELECT consultation.patient_id, consultation_anesth.listCim10
        FROM consultation_anesth
        LEFT JOIN consultation
        ON consultation.consultation_id = consultation_anesth.consultation_id
        WHERE listCim10 IS NOT NULL
        ORDER BY consultation.patient_id;";

$listConsult = db_loadlist($sql);

$n = count($listConsult);

foreach($listConsult as $key => $value) {
  $sql = "UPDATE patients" .
      "\nSET listCim10 = '".$value["listCim10"]."'" .
      "\nWHERE patient_id = '".$value["patient_id"]."';";
  db_exec( $sql ); db_error();
}

echo "$n diagnostics transférés";