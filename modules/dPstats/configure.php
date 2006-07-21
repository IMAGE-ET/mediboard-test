<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->display("configure.tpl");

?>