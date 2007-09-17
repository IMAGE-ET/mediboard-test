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

// Chargement de l'id400 "labo code4" du praticien
$prat =& $mbPrescription->_ref_praticien;
$tagCode4 = "labo code4";
$idSantePratCode4 = new CIdSante400();
$idSantePratCode4->loadLatestFor($prat, $tagCode4);

// Chargement de l'id400 "labo code9" du praticien
$tagCode9 = "labo code9";
$idSantePratCode9 = new CIdSante400();
$idSantePratCode9->loadLatestFor($prat, $tagCode9);

// Si le praticien n'a pas d'id400, il ne peut pas envoyer la prescription
if (!$idSantePratCode4->_id || !$idSantePratCode9->_id){
  $AppUI->setMsg("Le praticien n'a pas d'id400 pour le catalogue LABO", UI_MSG_ERROR );
  redirect();
}

$tagCatalogue = $dPconfig['dPlabo']['CCatalogueLabo']['remote_name'];

//Creation de l'id400
$idPresc = new CIdSante400();

//Paramétrage de l'id 400
$idPresc->tag = "$tagCatalogue Prat:".str_pad($idSantePratCode4->id400, 4, '0', STR_PAD_LEFT); // tag LABO Prat: 0017
$idPresc->object_class = "CPrescriptionLabo";              // object_class
$idPresc->loadMatchingObject("id400 DESC");                // Chargement de l'id 400 s'il existe

$idPresc->id400++;                                         // Incrementation de son id400
$idPresc->id400 = str_pad($idPresc->id400, 4, '0', STR_PAD_LEFT);

$idPresc->_id = null;
$idPresc->last_update = mbDateTime();
$idPresc->object_id = $mbPrescription->_id;
$idPresc->store();

// Gestion du sexe du patient
$transSexe["m"] = "1";
$transSexe["f"] = "2";
$transSexe["j"] = "2";



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

$transTitre["Monsieur"]      = "1"; 
$transTitre["Madame"]        = "2"; 
$transTitre["Mademoiselle"]  = "3"; 
$transTitre["Enfant garçon"] = "4"; 
$transTitre["Enfant fille"]  = "5"; 
$transTitre["Bébé garçon"]   = "6"; 
$transTitre["Bébé fille"]    = "7"; 
$transTitre["Docteur"]       = "8"; 
$transTitre["Doctoresse"]    = "A"; 


$doc->setDocument("tmp/Prescription-".$mbPrescription->_id.".xml");

// Creation de la prescription
$prescription     = $doc->addElement($doc, "Prescription");

// Prescription --> Numero
$num_prat = str_pad($idSantePratCode4->id400, 4, '0', STR_PAD_LEFT);
$num_presc = $idPresc->id400;
$num_presc %= 1000;
$num_presc = str_pad($num_presc, 4, '0', STR_PAD_LEFT);

$numero           = $doc->addElement($prescription, "numero", $num_prat.$num_presc);

// Prescription --> Patient
$patient          = $doc->addElement($prescription, "Patient");
$nom              = $doc->addElement($patient, "nom", $mbPatient->nom);
$prenom           = $doc->addElement($patient, "prenom", $mbPatient->prenom);
$titre            = $doc->addElement($patient, "titre", $transTitre[$titre_]);
$sexe             = $doc->addElement($patient, "sexe", $transSexe[$mbPatient->sexe]);
$datenaissance    = $doc->addElement($patient, "datenaissance", mbTranformTime(null, $mbPatient->naissance, "%Y%m%d"));
$adresseligne1    = $doc->addElement($patient, "adresseligne1", $mbPatient->adresse);
$adresseligne2    = $doc->addElement($patient, "adresseligne2", "");
$codepostal       = $doc->addElement($patient, "codepostal", $mbPatient->cp);
$ville            = $doc->addElement($patient, "ville", $mbPatient->ville);
$pays             = $doc->addElement($patient, "pays", $mbPatient->pays);
$assurance        = $doc->addElement($patient, "assurance", $mbPatient->regime_sante);

// Prescription --> Dossier 
$dossier          = $doc->addElement($prescription, "Dossier");
$dateprelevement  = $doc->addElement($dossier,"dateprelevement", mbTranformTime(null, mbDate($mbPrescription->date), "%Y%m%d"));
$heureprelevement = $doc->addElement($dossier,"heureprelevement", mbTime($mbPrescription->date));
$urgent           = $doc->addElement($dossier,"urgent", $mbPrescription->urgence);
$afaxer           = $doc->addElement($dossier,"afaxer", "");
$atelephoner      = $doc->addElement($dossier,"atelephoner", "");
 
// Prescription --> Analyse
$analyse          = $doc->addElement($prescription, "Analyse"); 
foreach ($mbPrescription->_ref_examens as $curr_analyse) {
  $code = $doc->addElement($analyse,"code", $curr_analyse->identifiant);
}


// Prescription -> Prescripteur
$prescripteur = $doc->addElement($prescription, "Prescripteur");
$code9 = $doc->addElement($prescripteur, "Code9", $idSantePratCode9->id400);
$code4 = $doc->addElement($prescripteur, "Code4", $idSantePratCode4->id400);


// Sauvegarde du fichier temporaire
$tmpPath = "tmp/dPlabo/export_prescription.xml";
CMbPath::forceDir(dirname($tmpPath));
$doc->save($tmpPath);
$doc->load($tmpPath);

// Validation du document
if (!$doc->schemaValidate()) {
  $AppUI->setMsg("Document de prescription non valide", UI_MSG_ERROR );
  redirect();
}

// Créer le document joint
if ($msg = $doc->addFile($mbPrescription)) {
  $AppUI->setMsg("Document non attaché à la prescription: $msg", UI_MSG_ERROR );
  redirect();
}


$AppUI->setMsg("Document envoyé", UI_MSG_OK );
redirect();

?>