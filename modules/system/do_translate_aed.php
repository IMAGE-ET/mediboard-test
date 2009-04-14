<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

// only user_type of Administrator (1) can access this page
$can->edit |= ($AppUI->user_type != 1);
$can->needsEdit();

$module = mbGetValueFromPost("module", null);
$trans  = mbGetValueFromPost("trans" , null);
$chaine = mbGetValueFromPost("chaine", null);

if(!$module || !$trans || !$chaine || !is_array($trans) || !is_array($chaine)){
  $AppUI->setMsg( "Certaines informations sont manquantes au traitement de la traduction.", UI_MSG_ERROR );
  $AppUI->redirect();
}

// If the locale files are in the module's "locales" directory
$in_module = (is_dir("modules/$module/locales"));

// Dossier des traductions
$localesDirs = array();
if ($in_module) {
  $files = glob("modules/$module/locales/*");
  foreach ($files as $file) {
    $name = basename($file, ".php");
    $localesDirs[$name] = $name;
  }
}
else {
  $localesDirs = $AppUI->readDirs("locales");
  CMbArray::removeValue(".svn",$localesDirs);
}

$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
foreach($localesDirs as $locale){
  // Données du fichier de langue  
  $translation = array();
  foreach($chaine as $key => $valChaine){
    if($valChaine!=""){
      $translation[$valChaine] = $trans[$key][$locale];
    }
  }
  
  //Ecriture du fichier
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = ($in_module ? "modules/$module/locales/$locale.php" : "locales/$locale/$module.php");
  $translateModule->update($translation, true);
}

$AppUI->setMsg( "Locales file saved", UI_MSG_OK );
$AppUI->redirect();
?>