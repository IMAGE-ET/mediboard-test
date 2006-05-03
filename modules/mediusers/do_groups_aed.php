<?php /* $Id: do_groups_aed.php,v 1.7 2005/10/04 10:56:49 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: 1.7 $
* @author Romain Ollivier
*/

global $AppUI;

require_once( $AppUI->getModuleClass('mediusers', 'groups') );
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CGroups", "group_id");
$do->createMsg = "Groupe créé";
$do->modifyMsg = "Groupe modifié";
$do->deleteMsg = "Groupe supprimé";
$do->doIt();

?>