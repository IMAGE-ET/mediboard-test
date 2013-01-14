<?php /* $Id$ */
/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Sébastien Fillonneau
*/

$templateManager = unserialize(gzuncompress($_SESSION["dPcompteRendu"]["templateManager"]));
header("Content-Type: text/javascript");

$user = CMediusers::get();
$use_apicrypt = false;
if ($user->mail_apicrypt && CModule::getActive("apicrypt")) {
  $use_apicrypt = true;
}

// Création du template
$smarty = new CSmartyDP("modules/dPcompteRendu");
$smarty->assign("templateManager", $templateManager);
$smarty->assign("nodebug", true);
$smarty->assign("use_apicrypt"  , $use_apicrypt);
$smarty->display("mb_fckeditor.tpl");