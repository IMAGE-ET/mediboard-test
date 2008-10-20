<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;
$ds = CSQLDataSource::get("std");
$ajax  = mbGetValueFromPost("ajax", 0);
$m     = mbGetValueFromPost("otherm", mbGetValueFromPost("m", ""));
$value = mbGetValueFromPost("value", 1);
$id    = mbGetValueFromPost("id", 0);

if($id) {
  $sql = "UPDATE sejour
          SET chambre_seule = '$value'
          WHERE sejour_id = '$id'";
  $result = $ds->exec($sql);
  $ds->error();
}

if($ajax) {
  $AppUI->getMsg();
  CApp::rip();
}

$AppUI->redirect("m=$m#adm$id");

?>