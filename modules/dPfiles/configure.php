<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $can;
$can->needsAdmin();

$listNbFiles = range(1,5);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listNbFiles"  , $listNbFiles);
$smarty->display("configure.tpl");

?>