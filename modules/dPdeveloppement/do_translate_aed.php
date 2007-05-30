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

$tabClass = mbGetClassByModule($module);

// liste des dossiers modules + common et styles
$modules = array_merge( array("common"=>"common", "styles"=>"styles") ,$AppUI->readDirs("modules"));
mbRemoveValuesInArray(".svn", $modules);
ksort($modules);

// Dossier des traductions
$localesDirs = $AppUI->readDirs("locales");
mbRemoveValuesInArray(".svn",$localesDirs);
mbRemoveValuesInArray("en",$localesDirs);

// Récupération du fichier demandé pour toutes les langues
$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
$contenu_file = array();
foreach($localesDirs as $locale){
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = "locales/fr/$modules[$module].php";
  $translateModule->load();
  $contenu_file[$locale] = $translateModule->values;
}

// Réattribution des clés et organisation
$trans = array();
foreach($localesDirs as $locale){
	foreach($contenu_file[$locale] as $k=>$v){
		$trans[ (is_int($k) ? $v : $k) ] = $v;
	}
}

//$tabTraduction = array_merge ($trans, $tableau);
ksort($tableau);

$translateModule = new CMbConfig;
$translateModule->sourcePath = null;

//Ecriture du fichier
$translateModule->options = array("name" => "locales");
$translateModule->targetPath = "locales/fr/$module.php";
$translateModule->update($tableau, true);  

$AppUI->setMsg( "Locales file saved", UI_MSG_OK );
$AppUI->redirect();
?>