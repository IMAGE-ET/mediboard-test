<?php

/**
 *	@package Mediboard
 *	@subpackage dPpersonnel
 *	@version $Revision: 
 *  @author Alexis Granger
 */

global $AppUI;

$do = new CDoObjectAddEdit("CPersonnel", "personnel_id");
$do->createMsg = "Personnel créé";
$do->modifyMsg = "Personnel modifié";
$do->deleteMsg = "Personnel supprimé";
$do->doIt();

?>