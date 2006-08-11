<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
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