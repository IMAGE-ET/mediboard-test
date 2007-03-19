<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $logPath;

$can->needsRead();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("view_logs.tpl");

global $logPath;

include($logPath);

?>