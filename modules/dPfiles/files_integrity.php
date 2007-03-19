<?php /* $Id: configure.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("files_integrity.tpl");

?>