<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
    $AppUI->redirect("m=system&a=access_denied");
}

// Récupération des données
$etablissement = mbGetValueFromPost("etablissement","Etablissement Test");
$_nb_pat       = mbGetValueFromPost("_nb_pat"      ,1);
$_nb_prat      = mbGetValueFromPost("_nb_prat"     ,1);
$_nb_fct       = mbGetValueFromPost("_nb_fct"      ,1);
$_nb_plages    = mbGetValueFromPost("_nb_plages"   ,1);
$_nb_consult   = mbGetValueFromPost("_nb_consult"  ,1);


$log_file = "tmp/echantillonnage.log";
$logFile = fopen($log_file, "a+");

$log_text = "##\r\n## Echantillonnage du ".date("d/m/Y")."\r\n##\r\n";
fwrite($logFile, $log_text);

$aNomFamille = array("ADAM" ,"ALBERT" ,"ALEXANDRE" ,"ANDRE" ,"ANTOINE" ,"ARNAUD" ,"AUBERT" ,"AUBRY" ,"BAILLY" ,"BARBE" ,"BARBIER" ,"BARON" ,"BARRE" ,"BARTHELEMY" ,"BENARD" ,"BENOIT" ,"BERGER" ,"BERNARD" ,"BERTIN" ,"BERTRAND" ,"BESNARD" ,"BESSON" ,"BIGOT" ,"BLANC" ,"BLANCHARD" ,"BLANCHET" ,"BONNET" ,"BOUCHER" ,"BOUCHET" ,"BOULANGER" ,"BOURGEOIS" ,"BOUSQUET" ,"BOUVIER" ,"BOYER" ,"BRETON" ,"BRIAND" ,"BRUN" ,"BRUNEL" ,"BRUNET" ,"BUISSON" ,"CAMUS" ,"CARLIER" ,"CARON" ,"CARPENTIER" ,"CARRE" ,"CHARLES" ,"CHARPENTIER" ,"CHARRIER" ,"CHAUVIN" ,"CHEVALIER" ,"CHEVALLIER" ,"CLEMENT" ,"COLAS" ,"COLIN" ,"COLLET" ,"COLLIN" ,"CORDIER" ,"COSTE" ,"COULON" ,"COURTOIS" ,"COUSIN" ,"DA SILVA" ,"DANIEL" ,"DAVID" ,"DELATTRE" ,"DELAUNAY" ,"DELMAS" ,"DENIS" ,"DESCHAMPS" ,"DEVAUX" ,"DIDIER" ,"DUBOIS" ,"DUFOUR" ,"DUMAS" ,"DUMONT" ,"DUPONT" ,"DUPUIS" ,"DUPUY" ,"DURAND" ,"DUVAL" ,"ETIENNE" ,"FABRE" ,"FAURE" ,"FERNANDEZ" ,"FERRAND" ,"FLEURY" ,"FONTAINE" ,"FOURNIER" ,"FRANCOIS" ,"GAILLARD" ,"GARCIA" ,"GARNIER" ,"GAUDIN" ,"GAUTHIER" ,"GAUTIER" ,"GAY" ,"GEORGES" ,"GERARD" ,"GERMAIN" ,"GILBERT" ,"GILLET" ,"GIRARD" ,"GIRAUD" ,"GONZALEZ" ,"GREGOIRE" ,"GRONDIN" ,"GROS" ,"GUERIN" ,"GUICHARD" ,"GUILLAUME" ,"GUILLET" ,"GUILLON" ,"GUILLOT" ,"GUILLOU" ,"GUYOT" ,"HAMON" ,"HARDY" ,"HEBERT" ,"HENRY" ,"HERVE" ,"HOARAU" ,"HUBERT" ,"HUET" ,"HUMBERT" ,"IMBERT" ,"JACOB" ,"JACQUES" ,"JACQUET" ,"JEAN" ,"JOLY" ,"JOUBERT" ,"JULIEN" ,"KLEIN" ,"LACROIX" ,"LAMBERT" ,"LAMY" ,"LANGLOIS" ,"LAPORTE" ,"LAUNAY" ,"LAURENT" ,"LE GALL" ,"LE GOFF" ,"LE ROUX" ,"LEBLANC" ,"LEBON" ,"LEBRETON" ,"LEBRUN" ,"LECLERC" ,"LECLERCQ" ,"LECOMTE" ,"LEDUC" ,"LEFEBVRE" ,"LEFEVRE" ,"LEGER" ,"LEGRAND" ,"LEGROS" ,"LEJEUNE" ,"LELIEVRE" ,"LEMAIRE" ,"LEMAITRE" ,"LEMOINE" ,"LEROUX" ,"LEROY" ,"LESAGE" ,"LEVEQUE" ,"LOPEZ" ,"LOUIS" ,"LUCAS" ,"MAHE" ,"MAILLARD" ,"MAILLOT" ,"MALLET" ,"MARCHAL" ,"MARCHAND" ,"MARECHAL" ,"MARIE" ,"MARTEL" ,"MARTIN" ,"MARTINEZ" ,"MARTY" ,"MASSE" ,"MASSON" ,"MATHIEU" ,"MAURY" ,"MENARD" ,"MERCIER" ,"MEUNIER" ,"MEYER" ,"MICHAUD" ,"MICHEL" ,"MILLET" ,"MONNIER" ,"MOREAU" ,"MOREL" ,"MORIN" ,"MORVAN" ,"MOULIN" ,"MULLER" ,"NICOLAS" ,"NOEL" ,"OLIVIER" ,"OLLIVIER" ,"PARIS" ,"PASCAL" ,"PASQUIER" ,"PAUL" ,"PAYET" ,"PELLETIER" ,"PEREZ" ,"PERRET" ,"PERRIER" ,"PERRIN" ,"PERROT" ,"PETIT" ,"PHILIPPE" ,"PICARD" ,"PICHON" ,"PIERRE" ,"PINEAU" ,"POIRIER" ,"PONS" ,"POULAIN" ,"PREVOST" ,"RAYMOND" ,"RAYNAUD" ,"REGNIER" ,"REMY" ,"RENARD" ,"RENAUD" ,"RENAULT" ,"REY" ,"REYNAUD" ,"RICHARD" ,"RIVIERE" ,"ROBERT" ,"ROBIN" ,"ROCHE" ,"RODRIGUEZ" ,"ROGER" ,"ROLLAND" ,"ROUSSEAU" ,"ROUSSEL" ,"ROUX" ,"ROY" ,"ROYER" ,"SANCHEZ" ,"SAUVAGE" ,"SCHMITT" ,"SCHNEIDER" ,"SIMON" ,"TANGUY" ,"TESSIER" ,"THOMAS" ,"VALLEE" ,"VASSEUR" ,"VERDIER" ,"VIDAL" ,"VINCENT" ,"VOISIN" ,"WEBER" );
$aPrenom_h   = array("Enzo" ,"Hugo" ,"Lucas" ,"Théo" ,"Mathéo" ,"Thomas" ,"Baptiste" ,"Léo" ,"Clément" ,"Louis" ,"Nathan" ,"Alexandre" ,"Quentin" ,"Romain" ,"Tom" ,"Mattéo" ,"Maxime" ,"Antoine" ,"Benjamin" ,"Mathis" ,"Valentin" ,"Robin" ,"Nicolas" ,"Paul" ,"Arthur" ,"Martin" ,"Éthan" ,"Julien" ,"Noah" ,"Victor" ,"Gabriel" );
$aPrenom_f   = array("Emma" ,"Clara" ,"Manon" ,"Anais" ,"Léa" ,"Chloé" ,"Lucie" ,"Camille" ,"Marie" ,"Jade" ,"Eva" ,"Louise" ,"Mathilde" ,"Julie" ,"Océane" ,"Laura" ,"Ilona" ,"Charlotte" ,"Emilie" ,"Sarah" ,"Clémence" ,"Lilou" ,"Justine" ,"Elisa" ,"Pauline" ,"Lisa" ,"Lena" ,"Lou" ,"Louane" ,"Maélis" ,"Perrine" );
$aPrenoms    = array("m"=>$aPrenom_h, "f"=>$aPrenom_f, "j"=>$aPrenom_f);

$aFct        = array("Chirurgie"     =>"cabinet",
                      "Administration"=>"administratif",
                      "Cabinet"       =>"cabinet",
                      "ORL"           =>"cabinet",
                      "Radiologie"    =>"cabinet");

$amediusers  = array(array("curr_type"=>"4"  ,"profil"=>"101"),
                      array("curr_type"=>"3"  ,"profil"=>"98"));

$group       = null;
$patients    = array();
$praticiens  = array();
$fonctions   = array();
$plageConsult= array();
$consults    = array();

// Création de l'établissement
$group = new CGroups();
$group->text = $etablissement;
$group->raison_sociale  = "[DEMO]";
$group->store();
$log_text = "CGroups: ".$group->_id."\r\n";

// Création des fonctions
$lstfct = array_rand($aFct,$_nb_fct);
if(!is_array($lstfct)){$lstfct = array($lstfct);}
foreach($lstfct as $keyFct){
  // Generation des couleur auto
  $color_r = dechex(rand(0,15));
  $color_v = dechex(rand(0,15));
  $color_b = dechex(rand(0,15));
  $fonction = new CFunctions();
  $fonction->text = $keyFct;
  $fonction->color = $color_r.$color_r.$color_v.$color_v.$color_b.$color_b;
  $fonction->group_id = $group->_id;
  $fonction->type = $aFct[$keyFct];
  $fonction->store();
  
  $fonctions[] = $fonction;
  $log_text .= "CFunctions: ".$fonction->_id."\r\n";
}

// Création des patients
for($i=1; $i<=$_nb_pat; $i++){
  $sexe = rand(0,2);
  $annee_max = date("Y");
  
  $patient = new CPatient();  
  if($sexe==0){
    $patient->sexe="m"; 
  }elseif($sexe==1){
    $patient->sexe="f";
    $annee_max = $annee_max-18;
  }else{
    $patient->sexe="j";
  }
  $key_prenom = array_rand($aPrenoms[$patient->sexe]);
  $key_nom    = array_rand($aNomFamille);
  
  $patient->prenom = $aPrenoms[$patient->sexe][$key_prenom];
  $patient->nom    = $aNomFamille[$key_nom];
  
  $patient->_jour  = rand(1,28);
  $patient->_mois  = rand(1,12);
  $patient->_annee = rand(1900,$annee_max);
  $patient->rques  = "[DEMO]";
  $patient->store();
  $patients[] = $patient;
  $log_text .= "CPatient: ".$patient->_id."\r\n";
}


// Création des Praticiens
for($i=1; $i<=$_nb_prat; $i++){
  $nom    = $aNomFamille[array_rand($aNomFamille)];
  $keyFct = array_rand($fonctions);
  $sexe   = rand(0,1);
  if($sexe){
    $prenom = $aPrenoms["f"][array_rand($aPrenoms["f"])];
  }else{
    $prenom = $aPrenoms["m"][array_rand($aPrenoms["m"])];  
  }
  $prat = new CMediusers;
  $prat->commentaires = "[DEMO]";
  $prat->function_id = $fonctions[$keyFct]->_id;
  
  if($i==1){
    // Premiere entrée : Pas d'aleatoire !
    $keytype = 0;
  }else{
    $keytype = array_rand($amediusers);
  }
  $_profile_id = $amediusers[$keytype]["profil"];
  $prat->_user_type  = $amediusers[$keytype]["curr_type"];
  $prat->_user_first_name = $prenom;
  $prat->_user_last_name  = $nom;
  $prat->_user_username   = str_replace(" ","",strtolower(substr($prenom,0,1).$nom));
  $prat->_user_password   = strtolower($prenom);
  $prat->store();
  
  $user = new CUser;
  $user->load($prat->_id);
  $msg = $user->copyPermissionsFrom($_profile_id, true);
  $prat->insFunctionPermission();
  $prat->insGroupPermission();

  $praticiens[$prat->_id] = $prat;
  $log_text .= "CMediusers: ".$prat->_id."\r\n";
}



// Création du tableau des possiblité de plages
$aPlages = array();
for($i=1; $i<=7; $i++){
  $aPlages[] = array("jour"=>$i, "meridiem"=>"am");
  $aPlages[] = array("jour"=>$i, "meridiem"=>"pm");
}
$today     = mbDate();
$debut     = mbDate("last sunday", $today);
$libelle   = array("postop","esthetique","generale","speciale");
$listMins  = array(00,15,30,45);
$listHours = array("am"=>array("debut"=>array(8,9,10),
                                "fin"  =>array(11,12,13)),
                    "pm"=>array("debut"=>array(14,15,16),
                                 "fin"  =>array(17,18,19,20)));

// Création des plages de consultations
foreach($praticiens as $prat){
  $plageConsult[$prat->_id] = array();
  $lstplages = array_rand($aPlages,$_nb_plages);
  if(!is_array($lstplages)){$lstplages = array($lstplages);}
  foreach($lstplages as $keyplages){
    $donnees_plage =& $aPlages[$keyplages];
    $plage_heure   =& $listHours[$donnees_plage["meridiem"]];
    $date = mbDate("+".$donnees_plage["jour"]." day", $debut);
    $key_heure_debut = array_rand($plage_heure["debut"]);
    $key_min_debut   = array_rand($listMins);
    $key_heure_fin   = array_rand($plage_heure["fin"]);
    $key_min_fin     = array_rand($listMins);
    $key_libelle     = array_rand($libelle);
    
    $plage = new CPlageconsult;
    $plage->date      = $date;
    $plage->_freq     = 15;
    $plage->_hour_deb = $plage_heure["debut"][$key_heure_debut];
    $plage->_min_deb  = $listMins[$key_min_debut];
    $plage->_hour_fin = $plage_heure["fin"][$key_heure_fin];
    $plage->_min_fin  = $listMins[$key_min_fin];
    $plage->libelle   = $libelle[$key_libelle];
    $plage->chir_id   = $prat->_id;
    
    if(!$plage->store()){
      $plageConsult[$prat->_id][] = $plage;
      $log_text .= "CPlageconsult: ".$plage->_id."\r\n";
    } 
  }
}



// Creation de consultations
$alistMotif = array(null,"visites postop","analyses","controle");

foreach($plageConsult as $keyChir=>$listplages){
  $listPlace = array();
  foreach($listplages as $plage){
    for ($i = 0; $i < $plage->_total; $i++) {
      $minutes = $plage->_freq * $i;
      $listPlace[] = array("plage_id"=>$plage->_id,
                            "heure"=>mbTime("+ $minutes minutes", $plage->debut),
                            "chir_id"=>$plage->chir_id);
      
    }
  }
  $listKeyPlaces = array_rand($listPlace,$_nb_consult);
  if(!is_array($listKeyPlaces)){$listKeyPlaces = array($listKeyPlaces);}
  foreach($listKeyPlaces as $keyPlace){
    $plage = $listPlace[$keyPlace];
    
    $premiere    = rand(0,3);
    $key_patient = array_rand($patients);
    $key_motif   = array_rand($alistMotif);
    
    
    $consult = new CConsultation;
    $consult->heure = $plage["heure"];
    if($premiere==0){
      $consult->_check_premiere = 1;
    }
    $consult->patient_id = $patients[$key_patient]->_id;
    $consult->plageconsult_id = $plage["plage_id"];
    $consult->motif = $alistMotif[$key_motif];
    
    if(!$msg = $consult->store()){
      $log_text .= "CConsultation: ".$consult->_id."\r\n";
      
      // Test Anesthésiste
      $chir_id = $plage["chir_id"];
      $praticiens[$chir_id]->updateFormFields();
      if($praticiens[$chir_id]->isFromType(array("Anesthésiste"))) {
        
        $consultAnesth = new CConsultAnesth;
        $consultAnesth->consultation_id = $consult->_id;  
        $consultAnesth->store();
        $log_text .= "CConsultAnesth: ".$consultAnesth->_id."\r\n";
      }
    }
  }
}

// A Faire : Creation de modeles
// A Faire : Creation d'Intervention / Séjour

fwrite($logFile, $log_text."\r\n");
fclose($logFile);

$AppUI->setMsg("Echantillonnage effectué",UI_MSG_OK)
?>