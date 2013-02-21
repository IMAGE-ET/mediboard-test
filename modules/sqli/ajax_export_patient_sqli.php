<?php

/**
 * Export patient database for audit
 *
 * @category Sqli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();
$separator = "|";
$now = mbDate();


$out = fopen('php://output', 'w');
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="Patients_sqli_export-'.$now.'.csv"');


$first_line = array(
  "id local",
  //---------------------------------------------
  "date naissance",
  "etat date de naissance",
  "nom 1",
  "nom usage",
  "vide",
  "prenom",
  "prenom 1",
  "prenom 2",
  "prenom 3",
  "zone vide",
  "zone vide",
  "sexe",
  "code postal commune naissance",
  "nom commune naissance",
  "code insee pays naissance",
  "ISO 3 pays naissance",
  "Numéro de département naissance",
  "indicateur de décès",
  "date de décès",
  //---------------------------------------------
  "etat de l'identité",
  "données validées par le patient",
  "Accord du DI pour le rapprochement",
  "accord du patient pour le rapprochement",
  "anonyme",
  "caché",
  "personnel hospitalier",
  "usurpation",
  //---------------------------------------------
  "numéro de rue",
  "nom de la rue",
  "premiere ligne (appart, couloir)",
  "deuxieme ligne (entree, bat)",
  "code postal",
  "nom de la ville",
  "departement",
  "code du pays INSEE",
  "code du pays ISO3",
  "Tel domicile",
  "Fax",
  "commentaires adresse",
  "tel portable",
  //---------------------------------------------
  "application (sys) ayant créé l'identité",
  "utilisateur ayant créé l'identité",
  "date de creation",
  "utilisateur (sys) ayant fait la derniere modif",
  "utilisateur ayant fait la derniere modif",
  "date de dernière modification",
  //---------------------------------------------
  "nationnalité (ISO3)",
  "situation de famille (HL7)",
  "catégorie socioprof (INSEE)",
  "tél professionnel",
  "num sécurité sociale"
);

fputcsv($out, $first_line, $separator);


$ligne = array();

$patient = new CPatient();
$ds = $patient->getDS();
$query = new CRequest();
$query->addSelect('patient_id');    //patient_id (id local)
$query->addSelect('naissance');     //date de naissance
$query->addSelect('"" as vide1');   //vide
$query->addSelect('nom');           //nom d'usage
$query->addSelect('nom_jeune_fille');   //nom de naissance
$query->addSelect('"" as vide2');       //vide 2
$query->addSelect('prenom');            //prenom
$query->addSelect('prenom_2');          //prenom 2
$query->addSelect('prenom_3');          //prenom 3
$query->addSelect('prenom_4');          //prenom 4
$query->addSelect('"" as vide3');       //vide 3
$query->addSelect('"" as vide4');       //vide 4
$query->addSelect('UPPER(sexe)');       //SEXE
$query->addSelect('cp_naissance');      //cp naissance
$query->addSelect('lieu_naissance');    //lieu naissance
$query->addSelect('pays_naissance_insee');  // insee pays naissance
$query->addSelect('"" as payscodeiso3');    //pays ISO3
$query->addSelect('"" as numeroDepartNaissance'); //numeroDepartementNaissance
$query->addSelect('"" as decesTrue');   //deces Vrai Faux
$query->addSelect('deces');             //date deces
$query->addSelect('"" as Etat2Identite'); //Etat de l'identite
$query->addSelect('"" as donneesValideeParLePatient');  //donnees validées par le patient
$query->addSelect('"" as AccordDuDIPourRapprochement');   //accord DI pour rapprochement
$query->addSelect('"" as AccordDUPatientPourRapprochement');  // accord du patient pour rapprochement
$query->addSelect('"" as anonyme');                           // anonyme
$query->addSelect('vip');                                     //caché
$query->addSelect('"" as PersonnelHospit');                   // est un membre du personnel
$query->addSelect('"" as Usurpation');                        //Usurpation
$query->addSelect('"" as Numero2Rue');                        //numéro de rue
$query->addSelect('"" as Nom2LaRue');                         // Nom de la rue
$query->addSelect('"" as ComplementAdresse');                 //complement adresse
$query->addSelect('"" as ComplementAdresse2');                //complement adresse 2
$query->addSelect('cp');
$query->addSelect('ville');
$query->addSelect('"" as departement');
$query->addSelect('"" as paysINSEE');
$query->addSelect('"" as paysISO3');
$query->addSelect('tel');
$query->addSelect('"" as fax');
$query->addSelect('"" as commentaireAdresse');
$query->addSelect('tel2');
$query->addSelect('"" as applicationCreatricePatient');
$query->addSelect('"" as UtilisateurCreateur');
$query->addSelect('"" as date_de_creation');
$query->addSelect('"" as applicationLastModif');
$query->addSelect('"" as utilisateurLastModif');
$query->addSelect('"" as dateLastModif');
$query->addSelect('"" as nationnalite');
$query->addSelect('UPPER(situation_famille)');
$query->addSelect('"" as catSocioProfessionnel');
$query->addSelect('"" as telPro');
$query->addSelect('matricule');

$query->addTable("patients");
$query->addOrder("patient_id");
$result = $ds->exec($query->getRequest());


while ($row = $ds->fetchAssoc($result)) {
  $row['naissance'] = ($row['naissance'] != '0000-00-00') ?  mbDateToLocale($row['naissance']) : " " ;
  $row['decesTrue'] = ($row['deces'] != '') ?  "Y" : "N" ;  //deces (indicateur)
  $row['deces'] = ($row['deces'] != '0000-00-00') ?  mbDateToLocale($row['deces']) : " " ;  //date deces
  $row['anonyme'] = (is_numeric($row["anonyme"])) ? "Y" : "N";  //anonymat
  $row['vip'] = ($row['vip']) ? "Y" : "N";  //VIP = caché
  fputcsv($out, $row, $separator, '"');
}

$nb_result = $ds->loadResult($query->getRequest());

//FAIRE UN FETCH

$ligne[] = $nb_result;

//------------------------------------------------------------------------------------------------------