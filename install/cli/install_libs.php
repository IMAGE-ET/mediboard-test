<?php
/**
 * External libraries installer
 *
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

require __DIR__ . "/bootstrap.php";

foreach (CLibrary::$all as $library) {
  if ($library->isInstalled() && $library->getUpdateState()) {
    continue;
  }

  $library->clearLibraries($library->name);

  echo "---- Installation de '$library->name' ----\n";
  if ($nbFiles = $library->install()) {
    echo " > $nbFiles fichiers extraits\n";
  }
  else {
    echo " > Erreur, $library->nbFiles fichiers trouvés\n";
  }

  echo " > Déplacement : ";
  if ($library->apply()) {
    echo "Ok";
  }
  else {
    echo "Erreur !";
  }
  echo "\n";

  if (count($library->patches)) {
    echo " > Application des patches : \n";
    foreach ($library->patches as $patch) {
      echo "  > Patch '$patch->sourceName' dans '$patch->targetDir': ";
      if ($patch->apply()) {
        echo "Patch appliqué";
      }
      else {
        echo "Erreur !";
      }
      echo "\n";
    }
  }

  echo "\n";
}
