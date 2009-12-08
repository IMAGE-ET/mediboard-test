<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $can;
$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listNbFiles"  , range(1,10));
$smarty->display("configure.tpl");

?>