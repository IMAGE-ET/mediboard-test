<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPportail
* @version $Revision: $
* @author Thomas Despoix
*/
 
global $AppUI, $can;
$can->needsRead();

$mbmail = new CMbMail();
$mbmail->from = $AppUI->user_id;
$mbmail->load(mbGetValueFromGetOrSession("mbmail_id"));
$mbmail->loadRefsFwd();

$users = $AppUI->_ref_user->loadUsers();

// Initialisation de FCKEditor
$templateManager = new CTemplateManager();
$templateManager->editor = "fckeditor";
$templateManager->simplifyMode = true;

if ($mbmail->date_sent) {
  $templateManager->printMode = true;
}

$templateManager->initHTMLArea();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mbmail", $mbmail);
$smarty->assign("users" , $users);

$smarty->display("write_mbmail.tpl");

?>