<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

$templateManager = new CTemplateManager;
$templateManager->valueMode = false;
$templateManager->initHTMLArea();

//$getMail = new Mail_IMAPv2();

// Création du template
$smarty = new CSmartyDP(1);

$smarty->display("send_mail.tpl");