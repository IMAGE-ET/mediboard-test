<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $can;

$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");
?>