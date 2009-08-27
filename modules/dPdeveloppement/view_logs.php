<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->needsRead();

// Création du template
$smarty = new CSmartyDP();
$smarty->display('view_logs.tpl');
