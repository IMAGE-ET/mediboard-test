<?php /* $Id: configure.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("configure.tpl");

?>