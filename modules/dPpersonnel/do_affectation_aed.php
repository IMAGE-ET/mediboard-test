<?php

/**
 *	@package Mediboard
 *	@subpackage dPpersonnel
 *	@version $Revision: 
 *  @author Alexis Granger
 */

// lignes pour rentrer l'heure courante du serveur dans certains champs
/*
$listTimes = array("_debut", "_fin");
foreach($listTimes as $curr_item) {
  if(isset($_POST[$curr_item])) {
    if($_POST[$curr_item] == "current") {
      $_POST[$curr_item] = mbTime();
    }
  }
}*/

$do = new CDoObjectAddEdit("CAffectationPersonnel", "affect_id");
$do->doIt();

?>