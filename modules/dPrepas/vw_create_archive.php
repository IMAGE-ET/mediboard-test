<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

CCanDo::checkAdmin();

$smarty = new CSmartyDP();
$smarty->display("vw_create_archive.tpl");
?>