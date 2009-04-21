<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("files_integrity.tpl");

?>