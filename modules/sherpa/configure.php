<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage sherpa
* @version $Revision: $
* @author Sherpa
*/

global $can;

$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("configure.tpl");

?>