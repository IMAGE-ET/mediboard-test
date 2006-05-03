<?php /* $Id: do_functions_aed.php,v 1.5 2005/10/04 10:56:49 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: 1.5 $
* @author Romain Ollivier
*/

global $AppUI;

require_once( $AppUI->getModuleClass('mediusers', 'functions') );
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CFunctions", "function_id");
$do->createMsg = "Fonction créée";
$do->modifyMsg = "Fonction modifiée";
$do->deleteMsg = "Fonction supprimée";
$do->doIt();

?>