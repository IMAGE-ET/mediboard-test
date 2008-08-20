<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

// Radio : debut et fin
/*
if(isset($_POST["radio_debut"])){
  if($_POST["radio_debut"] == "current"){
    $_POST["radio_debut"] = mbDateTime();
  }
}
if(isset($_POST["radio_fin"])){
  if($_POST["radio_fin"] == "current"){
    $_POST["radio_fin"] = mbDateTime();
  }
}

//Date courante dans la sortie
if(isset($_POST["sortie"])){
  if($_POST["sortie"] == "current"){
    $_POST["sortie"] = mbDateTime();
  }
}
*/
$do = new CDoObjectAddEdit("CRPU", "rpu_id");
$do->doIt();

?>