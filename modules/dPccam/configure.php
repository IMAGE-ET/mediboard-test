<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

// Création du template
$smarty = new CSmartyDP();

$smarty->display("configure.tpl");

?>