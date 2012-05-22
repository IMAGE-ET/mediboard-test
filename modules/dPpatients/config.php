<?php

$dPconfig["dPpatients"] = array (
  "CPatient"    => array (
    "tag_ipp"           => "",
    "tag_ipp_group_idex"=> "",
    "tag_ipp_trash"     => "trash_",
    "tag_conflict_ipp"  => "conflict_",
    "identitovigilence" => "nodate",
    "multi_group"       => "limited",
    "merge_only_admin"  => "0",
    "extended_print"    => "0",
    "adult_age"         => "15",
    "limit_char_search" => "0",
    "check_code_insee"  => "1",
    "show_patient_link" => "0"
  ),
  
  "CAntecedent" => array (
    "types"     => "med|alle|trans|obst|chir|fam|anesth|gyn",
    "appareils" => "cardiovasculaire|digestif|endocrinien|neuro_psychiatrique|pulmonaire|uro_nephrologique",
  ),
  
  "CTraitement" => array (
    "enabled" => "1",
  ),
  
  "CDossierMedical" => array (
  ),
  
  "CConstantesMedicales" => array(
    "important_constantes" => "poids|pouls|ta_gauche|temperature",
    "unite_ta" => "cmHg",
    "diuere_24_reset_hour" => "8",
    "redon_cumul_reset_hour" => "8",
    "sng_cumul_reset_hour" => "8",
    "lame_cumul_reset_hour" => "8",
    "drain_cumul_reset_hour" => "8",
    "drain_thoracique_cumul_reset_hour" => "8",
    "drain_pleural_cumul_reset_hour" => "8",
    "drain_mediastinal_cumul_reset_hour" => "8",
    "sonde_ureterale_cumul_reset_hour" => "8",
    "sonde_nephro_cumul_reset_hour" => "8",
    "sonde_vesicale_cumul_reset_hour" => "8",
    "show_enable_all_button" => "1",
  ),
  
  "CMedecin" => array(
   "medecin_strict" => "0",
  ),
  
  "INSEE" => array(
   "france" => "1",
   "suisse" => "0",
  ),
);

?>