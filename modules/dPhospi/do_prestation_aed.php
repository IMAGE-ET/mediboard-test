<?php

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision:
* @author Alexis Granger
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CPrestation", "prestation_id");
$do->createMsg = "Prestation créée";
$do->modifyMsg = "Prestation modifiée";
$do->deleteMsg = "Prestation supprimée";
$do->doIt();
?>