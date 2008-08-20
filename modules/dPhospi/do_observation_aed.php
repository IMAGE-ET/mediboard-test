<?php

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: 
 *  @author Romain Ollivier
 */
/*
if(isset($_POST["date"]) && $_POST["date"] == "now") {
  $_POST["date"] = mbDateTime();
}*/

$do = new CDoObjectAddEdit("CObservationMedicale", "observation_medicale_id");
$do->doIt();

?>