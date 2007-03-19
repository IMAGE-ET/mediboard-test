<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("configure.tpl");

?>