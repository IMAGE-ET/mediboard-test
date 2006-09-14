<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

// Création du template
$smarty = new CSmartyDP(1);

$smarty->display("configure.tpl");

?>