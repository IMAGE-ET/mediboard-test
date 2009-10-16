<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $version;

$can->needsEdit();

// Check locales
foreach (glob("locales/*", GLOB_ONLYDIR) as $localeDir) {
  $localeName = basename($localeDir);
  $locales = array();
  $localeFiles = array_merge(glob("locales/$localeName/*.php"), glob("modules/*/locales/$localeName.php"));
  foreach ($localeFiles as $localeFile) {
    if (basename($localeFile) != "meta.php") {
      require $localeFile;
    }
  }
  
  $path = "./tmp/locales.$localeName.js";
  if (!is_file($path)) {
    echo "<div class='warning'>Fichier de locales JS '$localeName' absent</div>";
    continue;
  }
  
  $fp = fopen($path, 'r');
  preg_match('#^//(\d+)#', fgets($fp), $v);
  if ($v[1] < $version['build']) {
    echo "<div class='warning'>Fichier de locales JS '$localeName' perimé</div>";
    continue;
  }

  if (null == $sharedLocale = SHM::get("locales-$localeName")) {
    echo "<div class='message'>Table absente en mémoire pour langage '$localeName'</div>";
    continue;
  }      
    
  if ($sharedLocale != $locales) {
    echo "<div class='warning'>Table périmée pour langage '$localeName'</div>";
    continue;
  }
  
  echo "<div class='message'>Table à jour pour langage '$localeName'</div>";
}

// Check class paths
$AppUI->getAllClasses();
$classNames = getChildClasses(null);
foreach($classNames as $className) {
  $class = new ReflectionClass($className);
  $classPaths[$className] = $class->getFileName();
}

if (null == $sharedClassPaths = SHM::get("class-paths")) {
  echo "<div class='message'>Table des classes absente en mémoire</div>";
  return;
}      
  
if ($sharedClassPaths != $classPaths) {
  echo "<div class='error'>Table des classes périmée</div>";
  return;
}

echo "<div class='message'>Table des classes à jour</div>";

