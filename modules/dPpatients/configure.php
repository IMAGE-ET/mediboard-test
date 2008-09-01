<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("pass", mbGetValueFromGet("pass"));
$smarty->display("configure.tpl");

?>