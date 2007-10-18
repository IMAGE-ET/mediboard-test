<?php /* $Id: edit_sorties.php 783 2006-09-14 12:44:01Z rhum1 $*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: 783 $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m, $g, $dPconfig;

// Il faut faire un test sur l'installation de sante400
$can->read &= (CModule::getInstalled("dPsante400") != null);
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

$patient_id = mbGetValueFromGetOrSession("patient_id");
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadIPP();
$patient->loadRefsSejours();

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
if($sejour_id) {
  if(isset($patient->_ref_sejours[$sejour_id])) {
    $sejour =& $patient->_ref_sejours[$sejour_id];
  } else {
    mbSetValueToSession("sejour_id");
    $sejour = new CSejour;
  }
} else {
  $sejour = new CSejour;
}
$sejour->loadNumDossier();

// Valeurs de démonstration
//$patient->_IPP        = "00073648";
//$sejour->_num_dossier = "07500684";

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("sejour" , $sejour);
$smarty->assign("idImeds", $idImeds);
$smarty->assign("url"    , $dPconfig["dPImeds"]["url"]);

$smarty->display("vw_results.tpl");

?>