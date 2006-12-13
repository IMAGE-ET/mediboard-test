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

$i = 0;
foreach (shm_list() as $key) {
  $i++;
  shm_rem($key);
}

echo "<div class='message'>$i variables supprimées</div>";