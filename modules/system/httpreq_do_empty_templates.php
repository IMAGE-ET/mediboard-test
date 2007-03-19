<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$i = 0;

foreach(glob("modules/*/templates_c/*.tpl.php") as $tplPath) {
  $i++;
  mbRemovePath($tplPath);
}
foreach(glob("style/*/templates_c/*.tpl.php") as $tplPath) {
  $i++;
  mbRemovePath($tplPath);
}

echo "<div class='message'>$i fichiers de cache supprimés</div>";