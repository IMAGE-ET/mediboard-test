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

$module = CValue::post("module", null);
$trans  = CValue::post("trans" , null);
$chaine = CValue::post("chaine", null);

if(!$module || !$trans || !$chaine || !is_array($trans) || !is_array($chaine)){
  CAppUI::setMsg( "Certaines informations sont manquantes au traitement de la traduction.", UI_MSG_ERROR );
  CAppUI::redirect();
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
  $localesDirs = CAppUI::readDirs("locales");
  CMbArray::removeValue(".svn",$localesDirs);
}

$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
foreach($localesDirs as $locale){
  // Données du fichier de langue  
  $translation = array();
  foreach($chaine as $key => $valChaine){
    if($valChaine != "" && $trans[$key][$locale] !== ""){
      $translation[$valChaine] = $trans[$key][$locale];
    }
  }
  
  // FIXME: Required not to delete the file if the array is empty
  if (count($translation) == 0) {
    $translation[$valChaine] = "";
  }
  
  uksort($translation, "strnatcmp");
  
  //Ecriture du fichier
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = ($in_module ? "modules/$module/locales/$locale.php" : "locales/$locale/$module.php");
  $translateModule->update($translation, false);
  
  SHM::rem("locales-$locale");
}

CAppUI::setMsg( "Locales file saved", UI_MSG_OK );
CAppUI::redirect();
