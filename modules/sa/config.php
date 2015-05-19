<?php 

$dPconfig["sa"] = array (
  "server" => "0",

  // Facturation
  "facture_codable_with_sejour" => "0",
  
  // Trigger
  "trigger_sejour"       => "facture",
  "trigger_operation"    => "facture",
  "trigger_consultation" => "cloture",
  
  // Send 
  "send_only_with_ipp_nda" => "0",
  "send_only_with_type"    => "",
  "send_actes_consult"     => "0",
  "send_actes_interv"      => "0",
  "send_diags_with_actes"  => "0"
);

