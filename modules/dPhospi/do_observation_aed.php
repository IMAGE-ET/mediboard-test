<?php

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: 
 *  @author Romain Ollivier
 */

if($_POST["date"] == "now") {
  $_POST["date"] = mbDateTime();
}

$do = new CDoObjectAddEdit("CObservationMedicale", "observation_medicale_id");
$do->doIt();

?>