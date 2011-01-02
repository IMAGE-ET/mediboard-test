<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Thomas Despoix
*/

CCanDo::checkRead();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("launch_tests.tpl");

?>