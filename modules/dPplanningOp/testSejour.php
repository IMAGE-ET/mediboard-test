<?php
$patient = new CPatient();
$patient_id = rand("72", "7488");
$patient->load("6076");

$sejour = new CSejour();
$sejour->patient_id = $patient->_id;
$sejour->praticien_id = "26";
$sejour->group_id = CGroups::loadCurrent()->_id;
$sejour->type = "comp";
$sejour->modalite = "libre";
$sejour->annule = "0";
$sejour->entree_prevue = "2013-07-05 08:00:00";
$sejour->sortie_prevue = "2013-07-16 10:00:00";
mbTrace($sejour->store());
?>