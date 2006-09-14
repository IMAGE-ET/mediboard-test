<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision$
 *  @author Romain Ollivier
 */

global $AppUI;

$do = new CDoObjectAddEdit("CSalle", "salle_id");
$do->createMsg = "Salle créée";
$do->modifyMsg = "Salle modifiée";
$do->deleteMsg = "Salle supprimée";
$do->doIt();

?>