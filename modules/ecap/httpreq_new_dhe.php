<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$patient = new CPatient();
$patient->load(mbGetValueFromGetOrSession("patient_id"));

$praticien = new CMediusers();
$praticien->load(mbGetValueFromGetOrSession("praticien_id"));

// Modules obligatoires
if (!CModule::getActive("ecap")) {
  CAppUI::stepAjax("Module 'ecap' has to be active");
}

if (!CModule::getActive("dPsante400")) {
  CAppUI::stepAjax("Module 'dPsante400' has to be active");
}

// Construction de l'URL
$urlDHE = CAppUI::conf("interop base_url");
if ($root_url = CAppUI::conf("ecap dhe rooturl")) {
  $urlDHE = $root_url . CMedicap::$paths["dhe"];
}

$urlDHEParams["logineCap"]       = "";
$urlDHEParams["codeAppliExt"]    = "mediboard";
$urlDHEParams["patIdentLogExt"]  = $patient->patient_id;
$urlDHEParams["patNom"]          = $patient->nom;
$urlDHEParams["patPrenom"]       = $patient->prenom;
$urlDHEParams["patNomJF"]        = $patient->nom_jeune_fille;
$urlDHEParams["patSexe"]         = $patient->sexe == "m" ? "1" : "2";
$urlDHEParams["patDateNaiss"]    = $patient->naissance;
$urlDHEParams["patAd1"]          = $patient->adresse;
$urlDHEParams["patCP"]           = $patient->cp;
$urlDHEParams["patVille"]        = $patient->ville;
$urlDHEParams["patTel1"]         = $patient->tel;
$urlDHEParams["patTel2"]         = $patient->tel2;
$urlDHEParams["patTel3"]         = "";
$urlDHEParams["patNumSecu"]      = substr($patient->matricule, 0, 13);
$urlDHEParams["patCleNumSecu"]   = substr($patient->matricule, 13, 2);
$urlDHEParams["interDatePrevue"] = "";

// Identifiant de l'établissement
$idExt = new CIdSante400;
$idExt->loadLatestFor(CGroups::loadCurrent(), "eCap");
$codeClinique = $urlDHEParams["codeClinique"] = $idExt->id400;

// Identifiant du patient dans l'établissement courant
$idExt = new CIdSante400;
$idExt->loadLatestFor($patient, "eCap CIDC:$codeClinique");
$urlDHEParams["patIdentEc"] = $idExt->id400;

// Identifiant du praticien dans l'établissement courant
$idExt = new CIdSante400;
$idExt->loadLatestFor($praticien, "eCap CIDC:$codeClinique");
$urlDHEParams["codePraticienEc"] = $idExt->id400;

// Problèmes pour une DHE
$noDHEReasons = array();
if (!$urlDHEParams["codeClinique"]) {
  $noDHEReasons[] = "codeClinique";
}
  
if (!$urlDHEParams["codePraticienEc"]) {
  $noDHEReasons[] = "codePraticienEc";
}

if (!$urlDHEParams["patDateNaiss"]) {
  $noDHEReasons[] = "patDateNaiss";
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("urlDHE"      , $urlDHE);
$smarty->assign("urlDHEParams", $urlDHEParams);
$smarty->assign("noDHEReasons", $noDHEReasons);

$smarty->display("inc_new_dhe.tpl");
?>