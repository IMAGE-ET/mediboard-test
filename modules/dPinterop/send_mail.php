<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$templateManager = new CTemplateManager;
$templateManager->valueMode = false;
$templateManager->initHTMLArea();

//$getMail = new Mail_IMAPv2();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("send_mail.tpl");