<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CBanque", "banque_id");
$do->createMsg = "Banque créée";
$do->modifyMsg = "Banque modifiée";
$do->deleteMsg = "Banque supprimée";
$do->doIt();

?>