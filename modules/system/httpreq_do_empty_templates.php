<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsEdit();

$i = 0;
foreach(glob("modules/*/templates_c/*") as $tplPath) {
  $i++;
  CMbPath::remove($tplPath);
}
foreach(glob("style/*/templates_c/*") as $tplPath) {
  $i++;
  CMbPath::remove($tplPath);
}

echo "<div class='message'>$i fichiers de cache supprimés</div>";