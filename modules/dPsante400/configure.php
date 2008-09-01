<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Thomas Despoix
*/

global $can;
$can->needsAdmin();

$types = CMouvFactory::getTypes();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("types", $types);
$smarty->display("configure.tpl");

?>