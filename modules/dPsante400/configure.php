<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

// Création du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");

?>