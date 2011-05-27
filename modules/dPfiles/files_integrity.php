<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkEdit();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("files_integrity.tpl");

?>