<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$i = 0;

foreach(glob("modules/*/templates_c/*.tpl.php") as $tplPath) {
  $i++;
  mbRemovePath($tplPath);
}

echo "<div class='message'>$i fichiers de cache supprimés</div>";