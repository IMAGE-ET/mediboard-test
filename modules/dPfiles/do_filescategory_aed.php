<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPfiles", "filescategory"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CFilesCategory", "files_category_id");
$do->createMsg = "Catégorie créée";
$do->modifyMsg = "Catégorie modifiée";
$do->deleteMsg = "Catégorie supprimée";
$do->doIt();

?>