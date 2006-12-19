<?php /* $Id: httpreq_do_empty_templates.php 982 2006-09-30 17:52:38Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 982 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Check locales
foreach (glob("locales/*", GLOB_ONLYDIR) as $localeDir) {
  $localeName = basename($localeDir);
  $locales = array();
  foreach (glob("locales/$localeName/*.php") as $localeFile) {
    if (basename($localeFile) != "encoding.php") {
      require $localeFile;
    }
  }

  if (null == $sharedLocale = shm_get("locales-$localeName")) {
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

if (null == $sharedClassPaths = shm_get("class-paths")) {
  echo "<div class='message'>Table des classes absente en mémoire</div>";
  return;
}      
  
if ($sharedClassPaths != $classPaths) {
  echo "<div class='error'>Table des classes périmée</div>";
  return;
}

echo "<div class='message'>Table des classes à jour</div>";

