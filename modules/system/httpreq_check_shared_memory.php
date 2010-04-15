<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $version;

$can->needsEdit();

// Check locales
foreach (glob("locales/*", GLOB_ONLYDIR) as $localeDir) {
  $localeName = basename($localeDir);
  $locales = array();
  $localeFiles = CAppUI::getLocaleFilesPaths($localeName);
  
  foreach ($localeFiles as $localeFile) {
    if (basename($localeFile) != "meta.php") {
      require $localeFile;
    }
  }
  
  $locales = array_filter($locales, "stringNotEmpty");
  
  $path = "./tmp/locales.$localeName.js";
  if (!is_file($path)) {
    CAppUI::stepAjax("Fichier de traductions JS '$localeName' absent", UI_MSG_WARNING);
    continue;
  }
  
  $fp = fopen($path, 'r');
  preg_match('#^//(\d+)#', fgets($fp), $v);
  if ($v[1] < $version['build']) {
    CAppUI::stepAjax("Fichier de traductions JS '$localeName' perimé", UI_MSG_WARNING);
    fclose($fp);
    continue;
  }

  if (null == $sharedLocale = SHM::get("locales-$localeName")) {
    CAppUI::stepAjax("Table absente en mémoire pour le langage '$localeName'", UI_MSG_OK);
    continue;
  }
  
  if ($sharedLocale != $locales) {
    CAppUI::stepAjax("Table périmée pour langage '$localeName'", UI_MSG_WARNING);
    continue;
  }
  
  CAppUI::stepAjax("Table à jour pour langage '$localeName'", UI_MSG_OK);
}

// Check class paths
CAppUI::getAllClasses();
$classNames = getChildClasses(null);
foreach($classNames as $className) {
  $class = new ReflectionClass($className);
  $classPaths[$className] = $class->getFileName();
}

if (null == $sharedClassPaths = SHM::get("class-paths")) {
  CAppUI::stepAjax("Table des classes absente en mémoire", UI_MSG_OK);
  return;
}      
  
if ($sharedClassPaths != $classPaths) {
  CAppUI::stepAjax("Table des classes périmée", UI_MSG_ERROR);
  return;
}

CAppUI::stepAjax("Table des classes à jour", UI_MSG_OK);
