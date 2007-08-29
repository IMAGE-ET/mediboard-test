<?php

/**
 *	@package Mediboard
 *	@subpackage dPpersonnel
 *	@version $Revision: 
 *  @author Alexis Granger
 */

global $AppUI;

$do = new CDoObjectAddEdit("CAffectationPersonnel", "affect_id");
$do->createMsg = "Affectation créée";
$do->modifyMsg = "Affectation modifiée";
$do->deleteMsg = "Affectation supprimée";
$do->doIt();

?>