<?php /* $Id: do_lit_aed.php,v 1.1 2005/04/04 09:07:16 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 1.1 $
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