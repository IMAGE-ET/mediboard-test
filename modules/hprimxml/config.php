<?php 

$dPconfig["hprimxml"] = array (
  // Extraction 
  "evt_serveuractes" => array(
    "validation" => "0",
    "version"    => "1.01",
    "send_ack"   => "1"  
  ),
  "evt_pmsi" => array(
    "validation" => "0",
    "version"    => "1.01",
    "send_ack"   => "1" 
  ),
  "evt_serveuretatspatient" => array(
    "validation" => "0",
    "version"    => "1.05",
    "send_ack"   => "1" 
  ),
  "evt_frais_divers" => array(
    "validation" => "0",
    "version"    => "1.05",
    "send_ack"   => "1" 
  ),
  "evt_patients" => array(
    "validation" => "0",
    "version"    => "1.05",
    "send_ack"   => "1" 
  ),
  "evt_mvtStock" => array(
    "validation" => "0",
    "version"    => "1.01",
    "send_ack"   => "1" 
  ),
  // Traitement
  "functionPratImport"         => "Import",
  "medecinIndetermine"         => "Medecin Indeterminé",
  "medecinActif"               => "0",
  "strictSejourMatch"          => "1",
  "notifier_sortie_reelle"     => "1",
  "notifier_entree_reelle"     => "1",
  "trash_numdos_sejour_cancel" => "0",
  "code_transmitter_sender"    => "mb_id",
  "code_receiver_sender"       => "dest",
  "date_heure_acte"            => "operation",
  
  // Schéma
  "concatenate_xsd"            => "0",
  "mvtComplet"                 => "0",
  "send_diagnostic"            => "evt_pmsi",
  "send_actes_ngap"            => "0"
);


?>