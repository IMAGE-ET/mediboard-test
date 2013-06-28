<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage Developpement
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

// Déverrouiller la session pour rendre possible les requêtes concurrentes.
CSessionHandler::writeClose();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("module", "dPdeveloppement");
$smarty->assign("action", "view_logs");
$smarty->display("benchmark.tpl");