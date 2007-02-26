<?php /* $Id: httpreq_do_empty_templates.php 982 2006-09-30 17:52:38Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 982 $
* @author Romain Ollivier
*/

// This script has to be lanuched via installer
// DO NOT $AppUI facilities
global $shm, $can;

// Only check permissions if connected to mediboard, and not to the installer
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

echo "<div class='message'>Table des classes suppprimée</div>";
