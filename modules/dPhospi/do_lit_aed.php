<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass($m, "lit"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CLit", "lit_id");
$do->createMsg = "Lit créé";
$do->modifyMsg = "Lit modifié";
$do->deleteMsg = "Lit supprimé";
$do->doIt();
?>