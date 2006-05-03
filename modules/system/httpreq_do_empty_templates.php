<?php /* $Id: httpreq_do_empty_templates.php,v 1.2 2006/04/25 15:06:34 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 1.2 $
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