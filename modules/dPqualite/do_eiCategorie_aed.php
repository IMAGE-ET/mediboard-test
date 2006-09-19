<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CEiCategorie", "ei_categorie_id");
$do->createMsg = "Catégorie créée";
$do->modifyMsg = "Catégorie modifiée";
$do->deleteMsg = "Catégorie supprimée";
$do->doIt();

?>