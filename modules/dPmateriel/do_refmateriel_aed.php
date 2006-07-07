<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPmateriel", "refmateriel"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CRefMateriel", "reference_id");
$do->createMsg = "Référence créée";
$do->modifyMsg = "Référence modifiée";
$do->deleteMsg = "Référence supprimée";
$do->doIt();

?>