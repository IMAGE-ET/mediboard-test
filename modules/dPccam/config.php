<?php 

$dPconfig["dPccam"] = array (
  "CCodeCCAM" => array(
    "use_cotation_ccam" =>"1",
    "use_new_ccam_architecture" => "CDatedCodeCCAM",
    'use_new_association_rules' => '0'
  ),
  "CCodable" => array (
    "use_getMaxCodagesActes" => "1",
    "add_acte_comp_anesth_auto" => "0",
    "use_frais_divers" => array(
      "CConsultation" => "0",
      "COperation" => "0",
      "CSejour" => "0"
    ),
    'lock_codage_ccam' => 'open'
  ),
);
