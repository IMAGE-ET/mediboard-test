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
$mbmail->from    = $AppUI->user_id;
$mbmail->to      = mbGetValueFromGet("to");
$mbmail->subject = mbGetValueFromGet("subject");
$mbmail->load(mbGetValueFromGetOrSession("mbmail_id"));
$mbmail->loadRefsFwd();

$functions = CMediusers::loadFonctions();
foreach($functions as &$curr_func) {
  $curr_func->loadRefsUsers();
}

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
$smarty->assign("functions" , $functions);

$smarty->display("write_mbmail.tpl");

?>