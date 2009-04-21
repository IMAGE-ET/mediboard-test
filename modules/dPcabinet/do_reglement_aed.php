<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Fabien Ménager
*/
/*
if (isset($_POST['date']) && $_POST['date'] == 'now') {
  $_POST['date'] = mbDateTime();
}*/

$do = new CDoObjectAddEdit('CReglement', 'reglement_id');

if(isset($_POST["_dialog"])) {
  $do->redirect = "m=dPcabinet&dialog=1&a=".$_POST["_dialog"];
  if(isset($_POST["_href"])) {
    $do->redirect .= "#".$_POST["_href"];
  }
}

$do->doIt();
?>