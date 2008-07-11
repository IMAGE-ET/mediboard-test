<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodSalvage
* @version $Revision:  $
* @author Alexandre Germonneau
*/


global $AppUI, $can, $m;
/*
 * Récupération de l'heure courante pour les timers de l'opération de Cell Saver.
 */
$listTimes = array("_recuperation_start", "_recuperation_end", "_transfusion_start", "_transfusion_end");
foreach($listTimes as $curr_item) {
  if(isset($_POST[$curr_item])) {
    if($_POST[$curr_item] == "current") {
      $_POST[$curr_item] = mbTime();
    }
  }
}
$do = new CDoObjectAddEdit('CBloodSalvage', 'blood_salvage_id');


$do->modifyMsg = "RSPO modifié";
$do->createMsg = "RSPO créé";
$do->doIt();

?>