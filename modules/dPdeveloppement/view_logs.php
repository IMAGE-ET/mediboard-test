<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m, $logPath;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Création du template
$smarty = new CSmartyDP();

$smarty->display("view_logs.tpl");

global $logPath;

include($logPath);

?>