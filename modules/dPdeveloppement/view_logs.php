<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Romain Ollivier
*/

global $can, $logPath;

$can->needsRead();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('logs', file_get_contents($logPath));
$smarty->display('view_logs.tpl');
