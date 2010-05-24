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
global $can;

// Only check permissions when connected to mediboard, not to the installer
if ($can) {
  $can->needsAdmin();
}

// Remove locales
foreach (glob("locales/*", GLOB_ONLYDIR) as $localeDir) {
  $localeName = basename($localeDir);
  $sharedName = "locales-$localeName";
  
  if (!SHM::get($sharedName)) {
    CAppUI::stepAjax("Table absente en mémoire pour langage '$localeName'", UI_MSG_OK);
    continue;
  }
  
  if (!SHM::rem($sharedName)) {
    CAppUI::stepAjax("Impossible de supprimer la table pour le langage '$localeName'", UI_MSG_ERROR);
    continue;
  }
  
  CAppUI::stepAjax("Table supprimée pour langage '$localeName'", UI_MSG_OK);
}

// Remove class paths
if (!SHM::get("class-paths")) {
  CAppUI::stepAjax("Table des classes absente en mémoire", UI_MSG_OK);
  return;
}
  
if (!SHM::rem("class-paths")) {
  CAppUI::stepAjax("Impossible de supprimer la table des classes", UI_MSG_ERROR);
  return;
}

CAppUI::stepAjax("Table des classes supprimée", UI_MSG_OK);

CJSLoader::writeLocaleFile();
CAppUI::stepAjax("Fichiers de locales mis à jour", UI_MSG_OK);
