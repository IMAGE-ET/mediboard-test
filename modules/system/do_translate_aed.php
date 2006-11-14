<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

// only user_type of Administrator (1) can access this page
if (!$canEdit || $AppUI->user_type != 1) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$module = mbGetValueFromPost("module", null);
$trans  = mbGetValueFromPost("trans" , null);
$chaine = mbGetValueFromPost("chaine", null);

if(!$module || !$trans || !$chaine || !is_array($trans) || !is_array($chaine)){
  $AppUI->setMsg( "Certaines informations sont manquantes au taitement de la traduction.", UI_MSG_ERROR );
  $AppUI->redirect();
}

// Dossier des traductions
$locales = $AppUI->readDirs("locales");
mbRemoveValuesInArray(".svn",$locales);

foreach($locales as $locale){
  // Données du fichier de langue
  $txt = "##\n## DO NOT MODIFY THIS FILE BY HAND!\n##\n";
  foreach($chaine as $key => $valChaine){
  	if($valChaine!=""){
  	  $txt .= "\"".stripslashes($valChaine)."\"=>\"".stripslashes($trans[$key][$locale])."\",\n";
  	}
  }
  //Ecriture du fichier
  if (!($fp = fopen ("{$AppUI->cfg['root_dir']}/locales/$locale/$module.inc", "wt"))) {
    $AppUI->setMsg( "Could not open locales file to save.", UI_MSG_ERROR );
    $AppUI->redirect( "m=system" );
  }
  fwrite( $fp, $txt );
  fclose( $fp );
}

$AppUI->setMsg( "Locales file saved", UI_MSG_OK );
$AppUI->redirect();
?>