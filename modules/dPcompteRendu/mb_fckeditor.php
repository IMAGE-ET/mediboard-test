<?php /* $Id: $ */
/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: $
* @author Sébastien Fillonneau
*/

$templateManager = unserialize($_SESSION["dPcompteRendu"]["templateManager"]);

// Création du template
$smarty = new CSmartyDP("modules/dPcompteRendu");
$smarty->assign("templateManager", $templateManager);
$smarty->assign("nodebug", true);
$smarty->display("mb_fckeditor.tpl");
?>