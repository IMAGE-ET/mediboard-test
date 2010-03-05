<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CCategory", "category_id");
$do->createMsg = "Catégorie créée";
$do->modifyMsg = "Catégorie modifiée";
$do->deleteMsg = "Catégorie supprimée";
$do->doIt();

?>