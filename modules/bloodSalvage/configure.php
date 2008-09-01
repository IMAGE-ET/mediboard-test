<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodsalvage
* @version $Revision: $
* @author Alexandre Germonneau
*/

global $can;
$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");

?>