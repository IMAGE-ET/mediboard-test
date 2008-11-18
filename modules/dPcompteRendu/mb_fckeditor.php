<?php /* $Id: $ */
/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: $
* @author Sébastien Fillonneau
*/

$mbPath = "../..";
$m = "dPcompteRendu";

class CTemplateManager {
  var $editor = "fckeditor";
  
  var $properties = array();
  var $helpers = array();
  var $lists = array();
  
  var $template = null;
  var $document = null;
  var $usedLists = array();
  
  // As wierd as it is, only this property seems mandatory in this redefinition
  var $valueMode = true;
  var $printMode = false;
};

// required includes for start-up
require_once("$mbPath/includes/config_dist.php");
require_once("$mbPath/includes/config.php");
require_once("$mbPath/includes/mb_functions.php");
require_once("$mbPath/includes/errors.php");
require_once("$mbPath/classes/ui.class.php");
require_once("$mbPath/includes/session.php");
require_once("$mbPath/classes/sharedmemory.class.php");
require_once("$mbPath/includes/autoload.php" );
require_once("$mbPath/includes/version.php" );

$templateManager =& $_SESSION[$m]["templateManager"];

CAppUI::requireSystemClass("smartydp");

// Création du template
$smarty = new CSmartyDP(CAppUI::conf("root_dir")."/modules/$m");

$smarty->assign("templateManager", $templateManager);
$smarty->assign("version", $version);
$smarty->assign("nodebug", true);

$smarty->display("mb_fckeditor.tpl");
?>