<?php /* $Id: configure.php 1738 2007-03-19 16:33:47Z maskas $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("configure.tpl");

?>