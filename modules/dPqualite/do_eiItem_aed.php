<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CEiItem", "ei_item_id");
$do->createMsg = "Item créé";
$do->modifyMsg = "Item modifié";
$do->deleteMsg = "Item supprimé";
$do->doIt();

?>