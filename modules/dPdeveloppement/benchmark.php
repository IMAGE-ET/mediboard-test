<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

// Déverrouiller la session pour rendre possible les requêtes concurrentes.
session_write_close();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("module", "dPdeveloppement");
$smarty->assign("action", "view_logs");
$smarty->display("benchmark.tpl");