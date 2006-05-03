<?php /* $Id: do_service_aed.php,v 1.2 2005/03/25 16:33:39 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 1.2 $
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