<?php /* $Id: edit_sorties.php 783 2006-09-14 12:44:01Z rhum1 $*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 783 $
* @author Romain OLLIVIER
*/

global $can, $g, $dPconfig;

$can->needsRead();

// Chargement des identifiants externes de l'établissement pour Imeds
$etablissement = new CGroups();
$etablissement->load($g);

$idImeds = array();
$id400 = new CIdSante400;

$id400->loadLatestFor($etablissement, "Imeds cidc");
$idImeds["cidc"] = $id400->id400;
$id400 = new CIdSante400;
$id400->loadLatestFor($etablissement, "Imeds cdiv");
$idImeds["cdiv"] = $id400->id400;
$id400 = new CIdSante400;
$id400->loadLatestFor($etablissement, "Imeds csdv");
$idImeds["csdv"] = $id400->id400;
$id400 = new CIdSante400;

$patient_id = mbGetValueFromGetOrSession("patient_id");
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadIPP();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("idImeds", $idImeds);
$smarty->assign("url"    , $dPconfig["dPImeds"]["url"]);

$smarty->display("inc_patient_results.tpl");