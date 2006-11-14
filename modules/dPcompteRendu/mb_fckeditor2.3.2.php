<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: $
* @author Sébastien Fillonneau
*/

$mbPath = "../../";
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
};
// required includes for start-up
require_once( $mbPath . "includes/config.php" );
require_once( $mbPath . "includes/main_functions.php" );
require_once( $mbPath . "classes/ui.class.php" );


// manage the session variable(s)
session_name("dotproject");
if (get_cfg_var("session.auto_start") > 0) {
  session_write_close();
}
session_start();

$AppUI =& $_SESSION["AppUI"];

$templateManager =& $_SESSION["dPcompteRendu"]["templateManager"];

// Création du template
require_once($AppUI->getSystemClass("smartydp"));

$smarty = new CSmartyDP(1);

$smarty->assign("templateManager" , $templateManager);

$smarty->display("mb_fckeditor2.3.2.tpl");
?>