<?php /* $Id: diag_patient.php,v 1.3 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 1.3 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcabinet', 'consultAnesth') );
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

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