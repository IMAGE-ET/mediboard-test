<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CMenu", "menu_id");
$do->createMsg = "Menu créé";
$do->modifyMsg = "Menu modifié";
$do->deleteMsg = "Menu supprimé";
$do->doIt();

?>