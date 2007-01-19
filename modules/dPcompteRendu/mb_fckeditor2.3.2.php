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
  var $editor = "fckeditor2.3.2";
  
  var $properties = array();
  var $helpers = array();
  var $lists = array();
  
  var $template = null;
  var $document = null;
  var $usedLists = array();
  
  // As wierd as it is, only this property seems mandatory in this redefinition
  var $valueMode = true;
};
// required includes for start-up
require_once("$mbPath/includes/config_dist.php");
require_once("$mbPath/includes/config.php");
require_once("$mbPath/classes/ui.class.php");
require_once("$mbPath/includes/session.php");
require_once("$mbPath/classes/sharedmemory.class.php");
require_once("$mbPath/includes/autoload.php" );
require_once("$mbPath/includes/main_functions.php" );

$templateManager =& $_SESSION[$m]["templateManager"];

// Création du template
require_once($AppUI->getSystemClass("smartydp"));

$smarty = new CSmartyDP();

$smarty->assign("templateManager" , $templateManager);

$smarty->display("mb_fckeditor2.3.2.tpl");
?>