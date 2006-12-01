<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->display("view_logs.tpl");

global $logPath;

include($logPath);

?>