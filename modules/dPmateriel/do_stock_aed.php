<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPmateriel", "stock"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CStock", "stock_id");
$do->createMsg = "Stock créé";
$do->modifyMsg = "Stock modifié";
$do->deleteMsg = "Stock supprimé";
$do->doIt();

?>