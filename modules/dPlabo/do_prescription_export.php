<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/


global $AppUI, $can, $m, $dPconfig;

function redirect() {
  global $AppUI;
  echo $AppUI->getMsg();
  exit;
}

if (!class_exists("DOMDocument")) {
  trigger_error("sorry, DOMDocument is needed");
  return;
}

$can->needsRead();

$doc = new CMbXMLDocument();

$doc->setSchema("modules/dPlabo/remote","prescription.xsd");
if (!$doc->checkSchema()) {
  $AppUI->setMsg("Schema manquant", UI_MSG_ERROR );
  redirect();
}

$mbPrescription = new CPrescriptionLabo();

// Chargement de la prescription
$mb_prescription_id = dPgetParam($_POST, "prescription_labo_id", null);
if(!$mb_prescription_id) {
  $AppUI->setMsg("Veuillez spécifier une prescription", UI_MSG_ERROR );
  redirect();
}
if ($mbPrescription->load($mb_prescription_id)) {
  $mbPrescription->loadRefs();
}

// Chargement de l'id400 LABO du praticien
// exemple: 0017
$prat =& $mbPrescription->_ref_praticien;
$tagCatalogue = $dPconfig['dPlabo']['CCatalogueLabo']['remote_name'];
$idSantePrat = new CIdSante400();
$idSantePrat->loadLatestFor($prat, $tagCatalogue);


// Si le praticien n'a pas d'id400, il ne peut pas envoyer la prescription
if (!$idSantePrat->_id){
  $AppUI->setMsg("Le praticien n'a pas d'id400 pour le catalogue LABO", UI_MSG_ERROR );
  redirect();
}


//Creation de l'id400
$idPresc = new CIdSante400();

//Paramétrage de l'id 400
$idPresc->tag = "$tagCatalogue Prat: ".str_pad($idSantePrat->id400, 4, '0', STR_PAD_LEFT); // tag LABO Prat: 0017
$idPresc->object_class = "CPrescriptionLabo";              // object_class
$idPresc->loadMatchingObject("id400 DESC");                // Chargement de l'id 400 s'il existe

$idPresc->id400++;                                         // Incrementation de son id400
$idPresc->id400 = str_pad($idPresc->id400, 4, '0', STR_PAD_LEFT);

$idPresc->_id = null;
$idPresc->last_update = mbDateTime();
$idPresc->object_id = $mbPrescription->_id;
$idPresc->store();

// Gestion du sexe du patient
$transSexe["m"] = "Masculin";
$transSexe["f"] = "Féminin";
$transSexe["j"] = "Féminin";

// Gestion des urgences
$transUrgence["0"] = "Non urgent";
$transUrgence["1"] = "Urgent";


$mbPatient =& $mbPrescription->_ref_patient;

// Gestion du titre du patient
if($mbPatient->sexe == "m"){
  if($mbPatient->_age >= 0 && $mbPatient->_age <= 3){
	$titre_ = "Bébé garçon";
  }
  if($mbPatient->_age > 3 && $mbPatient->_age < 18){
	$titre_ = "Enfant garçon";
  }
  if($mbPatient->_age >= 18){
	$titre_ = "Monsieur";
  }
  
}

if($mbPatient->sexe == "f" || $mbPatient->sexe == "j"){
  if($mbPatient->_age >= 0 && $mbPatient->_age <= 3){
	$titre_ = "Bébé fille";
  }
  if($mbPatient->_age > 3 && $mbPatient->_age < 18){
	$titre_ = "Enfant fille";
  }
  if($mbPatient->_age >= 18 && $mbPatient->nom_jeune_fille){
	$titre_ = "Madame";
  }
  if($mbPatient->_age >= 18 && !$mbPatient->nom_jeune_fille){
	$titre_ = "Mademoiselle";
  }
}





$doc->setDocument("tmp/Prescription-".$mbPrescription->_id.".xml");

// Creation de la prescription
$prescription     = $doc->addElement($doc, "Prescription");

// Prescription --> Numero
$num_prat = str_pad($idSantePrat->id400, 4, '0', STR_PAD_LEFT);
$num_presc = $idPresc->id400;
$num_presc %= 1000;
$num_presc = str_pad($num_presc, 4, '0', STR_PAD_LEFT);

$numero           = $doc->addElement($prescription, "numero", $num_prat.$num_presc);

// Prescription --> Patient
$patient          = $doc->addElement($prescription, "Patient");
$nom              = $doc->addElement($patient, "nom", $mbPatient->nom);
$prenom           = $doc->addElement($patient, "prenom", $mbPatient->prenom);
$titre            = $doc->addElement($patient, "titre", $titre_);
$sexe             = $doc->addElement($patient, "sexe", $transSexe[$mbPatient->sexe]);
$datenaissance    = $doc->addElement($patient, "datenaissance", $mbPatient->naissance);
$adresseligne1    = $doc->addElement($patient, "adresseligne1", $mbPatient->adresse);
$adresseligne2    = $doc->addElement($patient, "adresseligne2", "");
$codepostal       = $doc->addElement($patient, "codepostal", $mbPatient->cp);
$ville            = $doc->addElement($patient, "ville", $mbPatient->ville);
$pays             = $doc->addElement($patient, "pays", $mbPatient->pays);
$assurance        = $doc->addElement($patient, "assurance", $mbPatient->regime_sante);

// Prescription --> Dossier 
$dossier          = $doc->addElement($prescription, "Dossier");
$dateprelevement  = $doc->addElement($dossier,"dateprelevement", mbDate($mbPrescription->date));
$heureprelevement = $doc->addElement($dossier,"heureprelevement", mbTime($mbPrescription->date));
$urgent           = $doc->addElement($dossier,"urgent", $transUrgence[$mbPrescription->urgence]);
$afaxer           = $doc->addElement($dossier,"afaxer", "");
$atelephoner      = $doc->addElement($dossier,"atelephoner", "");
 
// Prescription --> Analyse
$analyse          = $doc->addElement($prescription, "Analyse"); 
foreach($mbPrescription->_ref_examens as $curr_analyse) {
  $code = $doc->addElement($analyse,"code", $curr_analyse->identifiant);
}

/*
$prescription    = $doc->addElement($doc, "prescription");
$doc->addAttribute($prescription, "id"  , $mbPrescription->_id);
$doc->addAttribute($prescription, "date", mbDate());
$nomPraticien    = $doc->addElement($prescription, "nomPraticien"   , $mbPrescription->_ref_praticien->_user_last_name);
$prenomPraticien = $doc->addElement($prescription, "prenomPraticien", $mbPrescription->_ref_praticien->_user_first_name);
$nomPatient      = $doc->addElement($prescription, "nomPatient"     , $mbPatient->nom);
$prenomPatient   = $doc->addElement($prescription, "prenomPatient"  , $mbPrescription->_ref_patient->prenom);
$date            = $doc->addElement($prescription, "date"           , mbDate($mbPrescription->date));
$analyses       = $doc->addElement($prescription, "analyses");
foreach($mbPrescription->_ref_examens as $curr_analyse) {
  $analyse = $doc->addElement($analyses, "analyse");
  $doc->addAttribute($analyse, "id", $curr_analyse->_id);
  $identifiant = $doc->addElement($analyse, "identifiant", $curr_analyse->identifiant);
  $libelle     = $doc->addElement($analyse, "libelle"    , $curr_analyse->libelle);
}
*/

if(!$doc->schemaValidate()) {
  $AppUI->setMsg("Document non valide", UI_MSG_ERROR );
  redirect();
}

$doc->addFile($mbPrescription);

$AppUI->setMsg("Document envoyé", UI_MSG_OK );
redirect();

?>