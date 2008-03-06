<?php /* $Id: send_mail.php 331 2006-07-13 14:26:26Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 331 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("module", "dPdeveloppement");
$smarty->assign("action", "view_logs");
$smarty->display("benchmark.tpl");