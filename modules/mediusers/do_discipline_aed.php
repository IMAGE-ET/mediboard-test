<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

require_once( $AppUI->getModuleClass('mediusers', 'discipline') );
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CDiscipline", "discipline_id");
$do->createMsg = "Discipline créée";
$do->modifyMsg = "Discipline modifiée";
$do->deleteMsg = "Discipline supprimée";
$do->doIt();

?>