<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CThemeDoc", "doc_theme_id");
$do->createMsg = "Thème créé";
$do->modifyMsg = "Thème modifié";
$do->deleteMsg = "Thème supprimé";
$do->doIt();

?>