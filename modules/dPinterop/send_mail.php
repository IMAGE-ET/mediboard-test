<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcompteRendu', 'templatemanager') );
//ini_set("include_path", ".;./lib/PEAR");
//require_once("Mail_IMAPv2/IMAPv2.php");

$templateManager = new CTemplateManager;
$templateManager->valueMode = false;
$templateManager->initHTMLArea();

//$getMail = new Mail_IMAPv2();

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP(1);

$smarty->display('send_mail.tpl');