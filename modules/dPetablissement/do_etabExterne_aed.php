<?php

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CEtabExterne", "etab_id");
$do->createMsg = "Etablissement externe créé";
$do->modifyMsg = "Etablissement externe modifié";
$do->deleteMsg = "Etablissement externe supprimé";
$do->doIt();

?>