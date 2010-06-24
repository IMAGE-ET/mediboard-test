<?php

$dPconfig["dPpatients"] = array (
  "CPatient"    => array (
    "tag_ipp"           => "",
    "tag_ipp_trash"     => "trash_",
    "tag_conflict_ipp"  => "conflict_",
    "identitovigilence" => "nodate",
    "multi_group"       => "limited",
    "merge_only_admin"  => "0",
    "extended_print"    => "0",
    "adult_age"         => "15",
    "limit_char_search" => "0",
  ),
  
  "CAntecedent" => array (
    "types"     => "med|alle|trans|obst|chir|fam|anesth|gyn",
    "appareils" => "cardiovasculaire|digestif|endocrinien|neuro_psychiatrique|pulmonaire|uro_nephrologique"
  ),
  
  "CTraitement" => array (
    "enabled" => "1",
  ),
  
  "intermax"    => array (
    "auto_watch" => "0",
  ),
  
  "CDossierMedical" => array (
    "diags_static_cim" => "1"
  ),
  
  "CConstantesMedicales" => array(
    "important_constantes" => "poids|pouls|ta|temperature"
  ),
  
  "CMedecin" => array(
   "medecin_strict" => "0"
  ),
);

?>