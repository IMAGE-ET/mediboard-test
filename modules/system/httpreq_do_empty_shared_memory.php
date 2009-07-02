<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// This script has to be launched via installer
// DO NOT USE $AppUI facilities
global $shm, $can;

// Only check permissions when connected to mediboard, not to the installer
if ($can) {
  $can->needsAdmin();
}

// Remove locales
foreach (glob("locales/*", GLOB_ONLYDIR) as $localeDir) {
  $localeName = basename($localeDir);
  $sharedName = "locales-$localeName";
  
  if (!$shm->get($sharedName)) {
    echo "<div class='message'>Table absente en mémoire pour langage '$localeName'</div>";
    continue;
  } 
  
  if (!$shm->rem($sharedName)) {
    echo "<div class='error'>Impossible de supprimer la table pour langage '$localeName'</div>";
    continue;
  }
  
  echo "<div class='message'>Table supprimée pour langage '$localeName'</div>";
}

// Remove class paths
if (!$shm->get("class-paths")) {
  echo "<div class='message'>Table des classes absente en mémoire</div>";
  return;
}      
  
if (!$shm->rem("class-paths")) {
  echo "<div class='error'>Impossible de supprimer la table des classes</div>";
  return;
}

echo "<div class='message'>Table des classes supprimée</div>";

mbWriteJSLocalesFile();
echo "<div class='message'>Fichiers de locales mis à jour</div>";
