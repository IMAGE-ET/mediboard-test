<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $dPconfig, $can, $m, $tab;

$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("configure.tpl");
?>