<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

//Date courante dans la sortie
if(isset($_POST["sortie"])){
  if($_POST["sortie"] == "current"){
    $_POST["sortie"] = mbDateTime();
  }
}

$do = new CDoObjectAddEdit("CRPU", "rpu_id");
$do->createMsg = "Urgence créée";
$do->modifyMsg = "Urgence modifiée";
$do->deleteMsg = "Urgence supprimée";
$do->doIt();

?>