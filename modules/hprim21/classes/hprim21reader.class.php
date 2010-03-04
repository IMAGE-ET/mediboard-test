<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain Ollivier
*/

class CHPrim21Reader {
  
  var $has_header                 = false;
  
  // Champs header
  var $separateur_champ           = null;
  var $separateur_sous_champ      = null;
  var $repetiteur                 = null;
  var $echappement                = null;
  var $separateur_sous_sous_champ = null;
  var $nom_fichier                = null;
  var $mot_de_passe               = null;
  var $id_emetteur                = null;
  var $id_emetteur_desc           = null;
  var $adresse_emetteur           = null;
  var $type_message               = null;
  var $tel_emetteur               = null;
  var $carac_trans                = null;
  var $id_recepteur               = null;
  var $id_recepteur_desc          = null;
  var $commentaire                = null;
  var $mode_traitement            = null;
  var $version                    = null;
  var $type                       = null;
  var $date                       = null;
  
  // Nombre d'éléments
   var $nb_patients = null;
  
  // Log d'erreur
  var $error_log = array();
  
  function readFile($fileName) {
    $file = fopen( $fileName, 'rw' );
    if(!$file) {
      $this->error_log[] = "Fichier non trouvé";
      return false;
    }
    $i = 0;
    $lines = array();
    while(!feof($file)){
      if(!$i) {
        $header = trim(fgets($file, 1024));
        $i++;
      } else {
        $curr_line = trim(fgets($file, 1024));
        if($curr_line) {
          // On vérifie si la ligne est un Addendum
          if(substr($curr_line, 0, 2) == "A|") {
            $lines[$i-1] .= substr($curr_line, 2);
          } else {
            $lines[$i] = $curr_line;
            $i++;
          }
        }
      }
    }
    
    // Lecture de l'en-tête
    if(!$this->segmentH($header)) {
      return false;
    }
    
    // Lecture du message
    switch($this->type_message) {
      // De demandeur (d'analyses ou d'actes de radiologie) à exécutant
      case "ADM" :
        // Transfert de données d'admission
        return $this->messageADM($lines);
        break;
      case "ORM" :
        // transfert de demandes d'analyses = prescription
        return $this->messageORM($lines);
        break;
      case "REG" :
        // transfert de données de règlement
        return $this->messageREG($lines);
        break;
      //D'exécutant à demandeur 
      case "ORU" :
        // transfert de résultats d'analyses
        return $this->messageORU($lines);
        break;
      case "FAC" :
        // transfert de données de facturation
        return $this->messageFAC($lines);
        break;
      // Bidirectionnel
      case "ERR" :
        // transfert de messages d'erreur
        return $this->messageERR($lines);
        break;
      default :
        $this->error_log[] = "Type de message non reconnu";
        return false;
    }
  }
  
  // Fonction de prise en charge des messages
  
  function messageADM($lines) {
    $nbLine = count($lines);
    $i = 1;
    while($i <= $nbLine && $this->getTypeLine($lines[$i]) == "P") {
      $patient = new CHprim21Patient();
      if(!$this->segmentP($lines[$i], $patient)) {
        return false;
      }
      $i++;
      if($i < $nbLine && $this->getTypeLine($lines[$i]) == "AP") {
        if(!$this->segmentAP($lines[$i], $patient)) {
          return false;
        }
        $i++;
        while($i < $nbLine && $this->getTypeLine($lines[$i]) == "AC") {
          $complementaire = new CHprim21Complementaire();
          if(!$this->segmentAC($lines[$i], $complementaire, $patient)) {
            return false;
          }
          $i++;
        }
      }
    }
    if(!isset($lines[$i]) || $this->getTypeLine($lines[$i]) != "L") {
      $this->error_log[] = "Erreur dans la suite des segments du message ADM";
      return false;
    }
    return $this->segmentL($lines[$i]);
  }
  function messageORM($lines) {
    $this->error_log[] = "Message ORM non pris en charge";
    return false;
  }
  function messageREG($lines) {
    $this->error_log[] = "Message REG non pris en charge";
    return false;
  }
  function messageORU($lines) {
    $this->error_log[] = "Message ORU non pris en charge";
    return false;
  }
  function messageFAC($lines) {
    $this->error_log[] = "Message FAC non pris en charge";
    return false;
  }
  function messageERR($lines) {
    $this->error_log[] = "Message ERR non pris en charge";
    return false;
  }
  
  // Fonctions de prise en charge des segments

  function getTypeLine($line) {
    $lines = explode($this->separateur_champ, $line);
    $type = reset($lines);
    return $type;
  }
  
  function segmentH($line) {
    if(strlen($line) < 6) {
      $this->error_log[] = "Segment header trop court";
      return false;
    }
    $this->separateur_champ           = $line[1];
    $this->separateur_sous_champ      = $line[2];
    $this->repetiteur                 = $line[3];
    $this->echappement                = $line[4];
    $this->separateur_sous_sous_champ = $line[5];
    $line = substr($line, 7);
    $champs = explode($this->separateur_champ, $line);
    if(count($champs) < 12) {
      $this->error_log[] = "Champs manquant dans le segment header";
      return false;
    }
    $this->nom_fichier       = $champs[0];
    $this->mot_de_passe      = $champs[1];
    $emetteur                = explode($this->separateur_sous_champ, $champs[2]);
    $this->id_emetteur       = $emetteur[0];
    $this->id_emetteur_desc  = $emetteur[1];
    $this->adresse_emetteur  = $champs[3];
    $this->type_message      = $champs[4];
    $this->tel_emetteur      = $champs[5];
    $this->carac_trans       = $champs[6];
    $recepteur               = explode($this->separateur_sous_champ, $champs[7]);
    $this->id_recepteur      = $recepteur[0];
    $this->id_recepteur_desc = $recepteur[1];
    $this->commentaire       = $champs[8];
    $this->mode_traitement   = $champs[9];
    $version_type            = explode($this->separateur_sous_champ, $champs[10]);
    $this->version           = $version_type[0];
    $this->type              = $version_type[1];
    $this->date              = $champs[11];
    $this->has_header        = true;
    return true;
  }
  function segmentP($line, &$patient) {
    if(!$this->has_header) {
      return false;
    }
    if(!$patient->bindToLine($line, $this)) {
      return false;
    }
    $patient->store();
    $medecin = new CHprim21Medecin();
    if($medecin->bindToLine($line, $this)) {
      if($medecin->external_id) {
        $medecin->store();
      }
    }
    $sejour = new CHprim21Sejour();
    if($sejour->bindToLine($line, $this, $patient, $medecin)) {
      if($sejour->external_id) {
        $sejour->store();
      }
    }
    return true;
  }
  function segmentOBR($line) {
    mbTrace($line, "Demande d'analyses ou d'actes");
    if(!$this->has_header) {
      return false;
    }
  }
  function segmentOBX($line) {
    mbTrace($line, "Résultat d'un test");
    if(!$this->has_header) {
      return false;
    }
  }
  function segmentC($line) {
    mbTrace($line, "Commentaire");
    if(!$this->has_header) {
      return false;
    }
  }
  function segmentL($line) {
    if(!$this->has_header) {
      return false;
    }
    return true;
  }
  function segmentA($line) {
    mbTrace($line, "Addendum");
    if(!$this->has_header) {
      return false;
    }
  }
  function segmentFAC($line) {
    mbTrace($line, "En-tête de facture");
    if(!$this->has_header) {
      return false;
    }
  }
  function segmentACT($line) {
    mbTrace($line, "Ligne de facture");
    if(!$this->has_header) {
      return false;
    }
  }
  function segmentREG($line) {
    mbTrace($line, "Elément de règlement");
    if(!$this->has_header) {
      return false;
    }
  }
  function segmentAP($line, &$patient) {
    if(!$this->has_header) {
      return false;
    }
    $patient->bindAssurePrimaireToLine($line, $this);
    $patient->store();
    return true;
  }
  function segmentAC($line, &$complementaire, $patient) {
    if(!$this->has_header) {
      return false;
    }
    $complementaire->bindToLine($line, $this, $patient);
    $complementaire->store();
    return true;
  }
  function segmentERR($line) {
    mbTrace($line, "Message d'erreur");
    if(!$this->has_header) {
      return false;
    }
  }

}

?>
