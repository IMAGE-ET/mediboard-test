<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

// only user_type of Administrator (1) can access this page
$can->edit |= ($AppUI->user_type != 1);
$can->needsEdit();

$module = mbGetValueFromPost("module", null);
$trans  = mbGetValueFromPost("trans" , null);
$chaine = mbGetValueFromPost("chaine", null);

if(!$module || !$trans || !$chaine || !is_array($trans) || !is_array($chaine)){
  $AppUI->setMsg( "Certaines informations sont manquantes au taitement de la traduction.", UI_MSG_ERROR );
  $AppUI->redirect();
}

// Dossier des traductions
$localesDirs = $AppUI->readDirs("locales");
mbRemoveValuesInArray(".svn",$localesDirs);

$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
foreach($localesDirs as $locale){
  // Données du fichier de langue  
  $translation = array();
  foreach($chaine as $key => $valChaine){
    if($valChaine!=""){
      $translation[$valChaine] = stripslashes($trans[$key][$locale]);
    }
  }
  //Ecriture du fichier
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = "locales/$locale/$module.php";
  $translateModule->update($translation, false);  
}

$AppUI->setMsg( "Locales file saved", UI_MSG_OK );
$AppUI->redirect();
?>