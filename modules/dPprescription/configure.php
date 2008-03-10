<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $dPconfig, $can, $m, $tab;

$can->needsAdmin();


// Création du template
$smarty = new CSmartyDP();


$smarty->display("configure.tpl");
?>