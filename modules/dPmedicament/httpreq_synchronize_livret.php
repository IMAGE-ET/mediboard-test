<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

global $can;
$can->needsAdmin();

$livretTherap = new CBcbProduitLivretTherapeutique();
$livretTherap->Synchronize();

CAppUI::stepAjax("Livret Thérapeutique synchronisé");

?>

