<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $can;
$can->needsAdmin();

$file = new CFile;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listNbFiles"  , range(1,10));
$smarty->assign("today", CMbDT::date());
$smarty->assign("nb_files", $file->countList() / 100);
$smarty->display("configure.tpl");

?>