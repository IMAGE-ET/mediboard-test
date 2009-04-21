<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsAdmin();

$smarty = new CSmartyDP();
$smarty->display("vw_create_archive.tpl");
?>