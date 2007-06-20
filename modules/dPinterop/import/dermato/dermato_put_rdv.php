<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();

set_time_limit( 1800 );

// Paramètres
$freq = "00:15:00";
$freqs = array (
  "00:15:00" => 1,
  "00:30:00" => 2,
  "00:45:00" => 3);

// Récupération des consultations
$sql = "SELECT dermato_import_rdv.*, dermato_import_praticiens.mb_id AS prat_mb_id, dermato_import_patients.mb_id AS patient_mb_id" .
    "\nFROM `dermato_import_rdv`, `dermato_import_patients`" .
    "\nLEFT JOIN `dermato_import_praticiens`" .
    "\nON dermato_import_rdv.praticien_id = dermato_import_praticiens.praticien_id" .
    "\nWHERE dermato_import_rdv.libelle NOT LIKE '%bloc opératoire%'" .
    "\nAND dermato_import_rdv.patient_id = dermato_import_patients.patient_id";
$res = db_exec($sql);
$rdv = array();
while ($row = db_fetch_object($res)) {
  $rdv[] = $row;
}

$nbPlagesCreees = 0;
$nbPlagesChargees = 0;
$nbRDVCreees = 0;
$nbRDVChargees = 0;

foreach ($rdv as $consult) {
  // vérification de l'existence de la plage
  $plage = new CPlageconsult();
  $listPlages = new CPlageconsult();
  $where = array(
    "chir_id" => "= '$consult->prat_mb_id'",
    "date"    => "= '$consult->date'");
  $plage->loadObject($where);
  $listPlages = $listPlages->loadList($where);
  foreach($listPlages as $key => $value) {
    if($value->debut <= $consult->debut && $value->fin >= $consult->debut) {
      $plage = new CPlageconsult();
      $plage->load($value->plageconsult_id);
    }
  }

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
  
  // Création de la consultation
  $consultation = new CConsultation;
  $sql = "SELECT consultation.*, plageconsult.*
        FROM consultation, plageconsult
        WHERE consultation.plageconsult_id = plageconsult.plageconsult_id
        AND consultation.patient_id = '$consult->patient_mb_id'
        AND plageconsult.date = '$consult->date'
        AND plageconsult.chir_id = '$consult->prat_mb_id'";
  $result = db_loadlist($sql);
  if(count($result))
    $consultation->load($result[0]["consultation_id"]);
  
  if ($consultation->consultation_id == null) {
    $consultation->plageconsult_id = $plage->plageconsult_id;
    $consultation->patient_id = $consult->patient_mb_id;
    
    $consultation->heure = $consult->debut;
    $consultation->duree = @$freqs[$consult->freq] or 1;
    $consultation->chrono = strftime("%Y-%m-%d") > $consult->date ? CConsultation::TERMINE : CConsultation::PLANIFIE;
    $consultation->annule = 0;
    $consultation->paye = strftime("%Y-%m-%d") > $consult->date ? 1 : 0;
    $consultation->motif = $consult->libelle;
    $consultation->premiere = ($consult->libelle == "CS 1ère fois");

    $consultation->store();
    $nbRDVCreees++;
  } else {
    $nbRDVChargees++;
  }
   
}

//mbTrace($limit, "limit start");
mbTrace($nbPlagesCreees, "nbPlagesCreees");
mbTrace($nbPlagesChargees, "nbPlagesChargees");
mbTrace($nbRDVCreees, "nbRDVCreees");
mbTrace($nbRDVChargees, "nbRDVChargees");

?>