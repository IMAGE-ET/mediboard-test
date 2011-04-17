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
  foreach($locales as &$_locale) {
    $_locale = CMbString::unslash($_locale);
  }
  
  $path = "./tmp/locales-$localeName.js";
  if (!is_file($path)) {
    CAppUI::stepAjax("Locales-javascript-cache-none", UI_MSG_WARNING, $localeName);
    continue;
  }
  
  $fp = fopen($path, 'r');
  preg_match('#^//(\d+)#', fgets($fp), $v);
  if ($v[1] < $version['build']) {
    CAppUI::stepAjax("Locales-javascript-cache-ko", UI_MSG_WARNING, $localeName);
    fclose($fp);
    continue;
  }

  if (null == $sharedLocale = SHM::get("locales-$localeName")) {
    CAppUI::stepAjax("Locales-shm-none", UI_MSG_OK, $localeName);
    continue;
  }
  
  if ($sharedLocale != $locales) {
    CAppUI::stepAjax("Locales-shm-ko", UI_MSG_WARNING, $localeName);
    continue;
  }
  
  CAppUI::stepAjax("Locales-shm-ok", UI_MSG_OK, $localeName);
}

// Check class paths
$classNames = CApp::getChildClasses(null);
foreach($classNames as $className) {
  $class = new ReflectionClass($className);
  $classPaths[$className] = $class->getFileName();
}

if (null == $sharedClassPaths = SHM::get("class-paths")) {
  CAppUI::stepAjax("Classes-shm-none", UI_MSG_OK);
}

// Only if there are missing classes, but nothing must happen if classes are added
if (array_intersect($sharedClassPaths, $classPaths) != $classPaths) {
  CAppUI::stepAjax("Classes-shm-ko", UI_MSG_WARNING);
}
else {
  CAppUI::stepAjax("Classes-shm-ok", UI_MSG_OK);
}

// Module specific checkings
foreach (glob("modules/*/check_shared_memory.php") as $script) {
  require $script;
}
