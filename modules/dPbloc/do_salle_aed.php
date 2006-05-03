<?php /* $Id: do_salle_aed.php,v 1.5 2005/12/10 19:03:23 rhum1 Exp $ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision: 1.5 $
 *  @author Romain Ollivier
 */

global $AppUI;

require_once($AppUI->getModuleClass('dPbloc', 'salle'));
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CSalle", "id");
$do->createMsg = "Salle créée";
$do->modifyMsg = "Salle modifiée";
$do->deleteMsg = "Salle supprimée";
$do->doIt();

?>