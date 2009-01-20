<?php

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

$prescription_id = mbGetValueFromGet("prescription_id");

// Chargement des praticiens
$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("prescription_id", $prescription_id);
$smarty->display("vw_signature_prescription.tpl");

?>