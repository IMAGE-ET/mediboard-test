<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

// Création du template
$smarty = new CSmartyDP();

$root_dir = CAppUI::conf('root_dir');

$smarty->assign("root_dir", $root_dir);
$smarty->display("test_ftp.tpl");

?>