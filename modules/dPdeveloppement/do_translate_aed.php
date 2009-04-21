<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Poiron Yohann
*/

global $AppUI, $can, $m;

// only user_type of Administrator (1) can access this page
$can->edit |= ($AppUI->user_type != 1);
$can->needsEdit();

$module = mbGetValueFromPost("module", null);
$tableau = mbGetValueFromPost("tableau", null);
$language = mbGetValueFromPost("language", null);

if(!$module || !$tableau || !is_array($tableau)){
  $AppUI->setMsg( "Certaines informations sont manquantes au traitement de la traduction.", UI_MSG_ERROR );
  $AppUI->redirect();
}

foreach ($tableau as $key => $valChaine){
    if ($valChaine){
      	$tableau[$key] = stripslashes($tableau[$key]);
    }
}

ksort($tableau);

$translateModule = new CMbConfig;
$translateModule->sourcePath = null;

//Ecriture du fichier
$translateModule->options = array("name" => "locales");
if (is_file("locales/$language/$module.php")) {
  $translateModule->targetPath = "locales/$language/$module.php";
} else {
  $translateModule->targetPath = "modules/$module/locales/$language.php";
}

$error = $translateModule->update($tableau, true);

if ($error instanceof PEAR_Error) {
  $AppUI->setMsg("Error while saving locales file : {$error->message}", UI_MSG_ERROR);
} else {
  $AppUI->setMsg( "Locales file saved", UI_MSG_OK );
  $AppUI->redirect();
}

?>