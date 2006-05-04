<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass($m, "service"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CService", "service_id");
$do->createMsg = "Service créé";
$do->modifyMsg = "Service modifié";
$do->deleteMsg = "Service supprimé";
$do->doIt();
?>