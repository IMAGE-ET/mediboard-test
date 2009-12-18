<?php
$patient = new CPatient();
$patient->nom = "anonyme";
$patient->prenom = "anonyme";
$patient->sexe = "m";
$patient->civilite = "m";
$patient->naissance = "06/12/1985";
$patient->store();

?>