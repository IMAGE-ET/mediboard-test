<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CFilesCategory", "file_category_id");
$do->createMsg = "Catégorie créée";
$do->modifyMsg = "Catégorie modifiée";
$do->deleteMsg = "Catégorie supprimée";
$do->doIt();

?>