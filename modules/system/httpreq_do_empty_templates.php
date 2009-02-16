<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
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