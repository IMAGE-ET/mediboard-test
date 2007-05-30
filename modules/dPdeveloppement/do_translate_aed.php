<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Poiron Yohann
*/

global $AppUI, $can, $m;

// only user_type of Administrator (1) can access this page
$can->edit |= ($AppUI->user_type != 1);
$can->needsEdit();

$module = mbGetValueFromPost("module", null);
$tableau = mbGetValueFromPost("tableau", null);

if(!$module || !$tableau || !is_array($tableau)){
  $AppUI->setMsg( "Certaines informations sont manquantes au traitement de la traduction.", UI_MSG_ERROR );
  $AppUI->redirect();
}

// Dossier des traductions
$localesDirs = $AppUI->readDirs("locales");
mbRemoveValuesInArray(".svn",$localesDirs);
mbRemoveValuesInArray("en",$localesDirs);

$translateModule = new CMbConfig;
$translateModule->sourcePath = null;

//Ecriture du fichier
$translateModule->options = array("name" => "locales");
$translateModule->targetPath = "locales/fr/$module.php";
$translateModule->update($tableau, false);  

$AppUI->setMsg( "Locales file saved", UI_MSG_OK );
$AppUI->redirect();
?>