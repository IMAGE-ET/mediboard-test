<?php 
$mbPath = "../../";
$m = "dPcompteRendu";
$dPconfig = array();

$connectorPath = "lib/fckeditor/editor/filemanager/browser/default/connectors/php/config.php";
require_once($mbPath . $connectorPath);

$runningUserFilePath  = $Config['UserFilesPath'];
$correctUserFilePath1 = "/Mediboard/UserFiles/";
$correctUserFilePath2 = "/dotproject/UserFiles/";

$runningEnabled = $Config['Enabled'];
$correctEnabled = true;

$configAlert = null;
/*
if (($runningUserFilePath != $correctUserFilePath1
     and $runningUserFilePath != $correctUserFilePath2)
    or $runningEnabled != $correctEnabled) {
  $configAlert = "FCKEditor file connector not configured properly." .
      "\n\nFile $connectorPath" .
      "\nShould contain these lines: " .
      "\n\$Config['UserFilesPath'] = '$correctUserFilePath';" .
      "\n\$Config['Enabled'] = 'true';";
}
*/
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
session_name( 'dotproject' );
if (get_cfg_var( 'session.auto_start' ) > 0) {
  session_write_close();
}
session_start();

$AppUI =& $_SESSION['AppUI'];
$AppUI->setConfig( $dPconfig );

// Get the template manager
$templateManager =& $_SESSION['dPcompteRendu']['templateManager'];

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));

$smarty = new CSmartyDP;
$smarty->debugging = false;

$smarty->assign("configAlert", $configAlert);
$smarty->assign("templateManager", $templateManager);
$smarty->assign("mb_version_build", $mb_version_build);

$smarty->display('mb_fckeditor.tpl');

?>
