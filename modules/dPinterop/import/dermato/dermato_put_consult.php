<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$limitConsult = mbGetValueFromGetOrSession("limitConsult", 0);
$ds = CSQLDataSource::get("std");
if ($limitConsult == -1) {
  return;	
}

$can->needsRead();

set_time_limit( 1800 );

// Param�tres
$freq = "00:15:00";
$freqs = array (
  "00:15:00" => 1,
  "00:30:00" => 2,
  "00:45:00" => 3);

// R�cup�ration des consultations
$sql = "SELECT " .
    "\ndermato_import_consultations1.*, " .
    "\ndermato_import_consultations2.*, " .
    "\ndermato_import_praticiens.mb_id AS prat_mb_id, " .
    "\ndermato_import_patients.mb_id AS patient_mb_id" .
    "\nFROM " .
    "\n`dermato_import_consultations1`, " .
    "\n`dermato_import_consultations2`, " .
    "\n`dermato_import_praticiens`, " .
    "\n`dermato_import_patients`" .
    "\nWHERE dermato_import_consultations1.chir_id = dermato_import_praticiens.praticien_id" .
    "\nAND dermato_import_consultations1.consultation1_id = dermato_import_consultations2.plageconsult_id" .
    "\nAND dermato_import_consultations2.patient_id = dermato_import_patients.patient_id" .
    "\nAND dermato_import_praticiens.praticien_id IN ('9', '10')" . // Liste des praticiens � prendre en compte
    "\nLIMIT $limitConsult, 1000";
$res = $ds->exec($sql);
$consults = array();
while ($row = $ds->fetchObject($res)) {
  $consults[] = $row;
}

$nbPlagesCreees = 0;
$nbPlagesChargees = 0;
$nbConsultationsCreees = 0;
$nbConsultationsChargees = 0;

foreach ($consults as $consult) {
  // v�rification de l'existence de la plage
  $plage = new CPlageconsult();
  $where = array(
    "chir_id" => "= '$consult->prat_mb_id'",
    "date"    => "= '$consult->date'");
  $plage->loadObject($where);

  if ($plage->plageconsult_id == null) {
    $plage->chir_id = $consult->prat_mb_id;
    $plage->date    = $consult->date;
    $plage->freq    = $freq;
    $plage->debut   = "09:00:00";
    $plage->fin     = "20:00:00";
    $plage->libelle = "Import Cobalys";
    $plage->store();
    $nbPlagesCreees++;
  } else {
    $nbPlagesChargees++;
  }
   
}

mbTrace($limitConsult, "limit start");
mbTrace($nbPlagesCreees, "nbPlagesCreees");
mbTrace($nbPlagesChargees, "nbPlagesChargees");
mbTrace($nbConsultationsCreees, "nbConsultationsCreees");
mbTrace($nbConsultationsChargees, "nbConsultationsChargees");

//$limitConsult = count($consults) ? $limitConsult + 1000 : -1;
//mbSetValueToSession("limitConsult", $limitConsult);
//header( 'refresh: 0; url=index.php?m=dPinterop&dialog=1&u=import/dermato&a=dermato_put_consult' );
?>