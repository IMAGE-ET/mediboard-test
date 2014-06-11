<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Record ADM
 */
class CHPrimSanteRecordADM extends CHPrimSanteMessageXML {

  public $identifier_patient;
  public $loop;

  /**
   * @see parent::getContentNodes
   */
  function getContentNodes() {
    $data = array();

    $this->queryNodes("//P", null, $data, true); // get ALL the P segments

    return $data;
  }

  /**
   * handle
   *
   * @param CHPrimSanteAcknowledgment $ack    Acknowledgment
   * @param CPatient                  $object Patient
   * @param array                     $data   data
   *
   * @return CHPrimSanteAcknowledgment|void
   */
  function handle($ack, CPatient $object, $data) {
    /** @var CExchangeHprimSante $exchange_hpr */
    $exchange_hpr = $this->_ref_exchange_hpr;
    /** @var CInteropSender $sender */
    $sender       = $this->_ref_sender;
    $sender->loadConfigValues();
    $erreur = array();
    //parcours des patients
    foreach ($data["//P"] as $_i => $_patient) {
      $this->loop = $_i;
      $identifier = $this->identifier_patient = $this->getPersonIdentifiers($_patient);

      if (!$identifier["identifier"]) {
        //identifier non transmis
        $erreur["P"][] = new CHPrimSanteError($exchange_hpr, "P", "01", array("P", $_i+1, $identifier), "8.3");
        continue;
      }
      //récupération du patient par idex/match
      $patient = $this->getPatient($identifier["identifier"], $_patient);
      //choix de l'action à effectué
      switch ($sender->_configs["action"]) {
        //sauvegarde de l'IPP et du NDA uniquement
        case "IPP_NDA":
          if (!$patient->_id) {
            //patient non trouvé
            $erreur[] = new CHPrimSanteError($exchange_hpr, "P", "02", array("P", $_i+1, $identifier), "8.3");
            break;
          }
          $ipp = $this->storeIdex($patient, $identifier["identifier"], $sender->_tag_patient);
          if ($ipp) {
            //sauvegarde de l'ipp impossible
            $erreur[] = new CHPrimSanteError($exchange_hpr, "P", "03", array("P", $_i+1, $identifier), "8.3");
            break;
          }
          //récupération de l'identifiant du sejour
          $nda_identifier = $this->getSejourIdentifier($_patient);
          //récupération du séjour idex/match
          $sejour = $this->getSejour($patient, $nda_identifier["sejour_identifier"], $_patient);
          if ($sejour instanceof CHPrimSanteError) {
            //problème lors de la récupération du séjour
            $erreur[] = $sejour;
            break;
          }
          //cas d'une modification d'un patient
          if ($sejour === null) {
            break;
          }
          $nda = $this->storeIdex($sejour, $nda_identifier["sejour_identifier"], $sender->_tag_sejour);
          if ($nda) {
            //sauvegarde du nda impossible
            $erreur[] = new CHPrimSanteError($exchange_hpr, "P", "07", array("P", $_i+1, $identifier), "8.5");
            break;
          }
          break;
        //sauvegarde du patient uniquement
        case "Patient":
          $result = $this->storePatient($identifier, $patient, $_patient);
          if ($result) {
            //problème de sauvegarde du patient
            $erreur[] = $result;
            break;
          }
          $ipp = $this->storeIdex($patient, $identifier["identifier"], $sender->_tag_patient);
          if ($ipp) {
            $erreur[] = new CHPrimSanteError($exchange_hpr, "P", "03", array("P", $_i+1, $identifier), "8.3");
            break;
          }
          break;
        //sauvegarde du patient et du séjour
        case "Patient_Sejour":
          $result = $this->storePatient($identifier, $patient, $_patient);
          if ($result) {
            $erreur[] = $result;
            break;
          }
          $ipp = $this->storeIdex($patient, $identifier["identifier"], $sender->_tag_patient);
          if ($ipp) {
            $erreur[] = new CHPrimSanteError($exchange_hpr, "P", "03", array("P", $_i+1, $identifier), "8.3");
            break;
          }
        //sauvegarde du séjour uniquement
        case "Sejour":
          //récupération de l'identifiant du sejour
          $nda_identifier = $this->getSejourIdentifier($_patient);
          //récupération du séjour idex/match
          $sejour = $this->getSejour($patient, $nda_identifier["sejour_identifier"], $_patient, true);
          if ($sejour instanceof CHPrimSanteError) {
            //problème lors de la récupération du séjour
            $erreur[] = $sejour;
            break;
          }
          //cas d'une modification d'un patient
          if ($sejour === null) {
            break;
          }

          $sejour = $this->storeSejour($sejour, $nda_identifier, $_patient);
          if ($sejour instanceof CHPrimSanteError) {
            $erreur[] = $sejour;
            break;
          }

          $nda = $this->storeIdex($sejour, $nda_identifier["sejour_identifier"], $sender->_tag_sejour);
          if ($nda) {
            //sauvegarde du nda impossible
            $erreur[] = new CHPrimSanteError($exchange_hpr, "P", "07", array("P", $_i+1, $identifier), "8.5");
            break;
          }
          break;

        default:
      }
    }

    return $exchange_hpr->setAck($ack, $erreur, $patient);
  }

  /**
   * get the patient
   *
   * @param String  $identifier identifier
   * @param DOMNode $node       node
   *
   * @return CPatient
   */
  function getPatient($identifier, $node) {
    $sender = $this->_ref_sender;

    $idex = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $identifier);

    $patient   = new CPatient();
    if ($idex->_id) {
      return $patient->load($idex->object_id);
    }

    $person    = $this->getNamePerson($node);
    $patient->nom       = $person["name"];
    $patient->prenom    = $person["firstname"];
    $patient->naissance = $this->getBirthdate($node);
    $patient->loadMatchingPatient();

    return $patient;
  }

  /**
   * get the sejour
   *
   * @param CPatient $patient    patient
   * @param String   $identifier identifier
   * @param DOMNode  $node       node
   *
   * @return CSejour|CHPrimSanteError
   */
  function getSejour($patient, $identifier, $node, $create = false) {
    $sender     = $this->_ref_sender;
    $patient_id = $patient->_id;

    $idex = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $identifier);

    $sejour = new CSejour();
    if ($idex->_id) {
      $sejour->load($idex->object_id);
      if ($sejour->patient_id != $patient_id) {
        return new CHPrimSanteError($this->_ref_exchange_hpr, "T", "13", array("P", $this->loop+1, $this->identifier_patient), "8.5");
      }
    }
    $sejour->patient_id = $patient_id;

    $data   = $this->getSejourStatut($node);
    $entree = $data["entree"] ? CMbDT::dateTime($data["entree"]): null;
    $sortie = $data["sortie"] ? CMbDT::dateTime($data["sortie"]): null;

    switch ($data["statut"]) {
      case "OP":
        $sejour->sortie_reelle = $sortie;
        break;
      case "IP":
        $sejour->type = "comp";
        $sejour->entree_reelle = $entree;
        $sejour->sortie_prevue = $sortie;
        break;
      case "IO":
        $sejour->type = "ambu";
        $sejour->entree_reelle = $entree;
        $sejour->sortie_prevue = $sortie;
        break;
      case "ER":
        $sejour->type = "urg";
        $sejour->entree_reelle = $entree;
        break;
      case "PA":
        $sejour->type = "comp";
        $sejour->entree_prevue = $entree;
        break;
      case "MP":
        $localisation = $this->getLocalisation($node);
        if (!$data["entree"] && !$localisation) {
          return null;
        }
        return new CHPrimSanteError($this->_ref_exchange_hpr, "T", "05", array("P", $this->loop+1, $this->identifier_patient), "8.25");
        break;

      default:
        return new CHPrimSanteError($this->_ref_exchange_hpr, "P", "05", array("P", $this->loop+1, $this->identifier_patient), "8.25");
    }
    if ($sejour->_id) {
      return $sejour;
    }

    $count = $sejour->loadMatchingSejour();

    if ($count > 1) {
      return new CHPrimSanteError($this->_ref_exchange_hpr, "P", "04", array("P", $this->loop+1, $this->identifier_patient), "8.25");
    }
    //si on est en modification et qu'aucun séjour n'a été retrouvé
    if (!$create && !$sejour->_id) {
      return new CHPrimSanteError($this->_ref_exchange_hpr, "P", "06", array("P", $this->loop+1, $this->identifier_patient), "8.25");
    }

    return $sejour;
  }

  /**
   * store the idex
   *
   * @param CPatient|CSejour $object     patient or sejour
   * @param String[]         $identifier identifiers
   * @param String           $tag        tag
   *
   * @return null|String
   */
  function storeIdex($object, $identifier, $tag) {
    $idex        = new CIdSante400();
    $idex->tag   = "$tag";
    $idex->id400 = "$identifier";
    $idex->setObject($object);
    $idex->loadMatchingObject();

    if ($msg = $idex->store()) {
      return $msg;
    }

    return null;
  }

  /**
   * store the patient
   *
   * @param String[] $identifier identifier
   * @param CPatient $patient    patient
   * @param DOMNode  $node       node
   *
   * @return null|CHPrimSanteError
   */
  function storePatient($identifier, $patient, $node) {
    $sender = $this->_ref_sender;

    if ($identifier["merge"] === "FU") {
      return $this->mergePatient($identifier, $sender->_tag_patient, $patient);
    }
    //@todo voir pour nouveau né
    /**
     * H2.3C: Si le patient est un nouveau né, les champs 8.3 et 8.5 peuvent contenir les numéros permanent et
     * dossier administratif de la mère. Dans ce cas, afin de distinguer (en plus des nom et prénom) le nouveau né de sa mère,
     * et pour permettre une meilleure gestion des admissions, il faut renseigner le sous-champ 8.5.2, dans les contextes ADM et ORM.
     */

    $person = $this->getNamePerson($node);

    $patient->nom_jeune_fille = $person["family_name"];
    $patient->nom             = $person["name"];
    $patient->prenom          = $person["firstname"];
    $patient->prenom_2        = $person["secondname"];
    $patient->naissance       = $this->getBirthdate($node);
    $patient->sexe            = CMbString::lower($this->getSexPerson($node));

    $address = $this->getAddress($node);
    $address["street"] .= $address["comp"] ? "\n{$address["comp"]}" : null;
    $patient->adresse = $address["street"];
    $patient->ville   = $address["city"];
    $patient->pays    = $address["country"];
    $patient->cp      = $address["postal"];

    $phone = $this->getPhone($node);
    $patient->tel  = CMbArray::get($phone, 0);
    $patient->tel2 = CMbArray::get($phone, 1);

    $patient->situation_famille = $this->getMaritalStatus($node);

    $patient->deces = $this->getDeathDate($node);

    if ($msg = $patient->store()) {
      return new CHPrimSanteError($this->_ref_exchange_hpr, "P", "08", array("P", $this->loop, $this->identifier_patient), "8.3", $msg);
    }

    /*$ins = $this->getINS($node);

    foreach ($ins as $_ins) {
      $ins_patient           = new CINSPatient();
      $ins_patient->ins      = $_ins["ins"];
      $ins_patient->type     = substr($_ins["type"], -1);
      $ins_patient->date     = $_ins["date"];
      $ins_patient->provider = $sender->nom;

      if ($msg = $ins_patient->store()) {
        return new CHPrimSanteError($this->_ref_exchange_hpr, "P", "11", array("P", $this->loop, $this->identifier_patient), "8.12", $msg);
      }
    }*/

    return null;
  }

  /**
   * store sejour
   *
   * @param CSejour  $sejour     sejour
   * @param String[] $identifier identifier
   * @param DOMNode  $node       node
   *
   * @return CHPrimSanteError|null
   */
  function storeSejour($sejour, $identifier, $node) {
    /** @var CInteropSender $sender */
    $sender               = $this->_ref_sender;
    $mediuser             = $this->getDoctor($node);
    if ($mediuser && !$mediuser->_id) {
      return new CHPrimSanteError($this->_ref_exchange_hpr, "P", "15",  array("P", $this->loop, $this->identifier_patient), "8.14");
    }
    $sejour->praticien_id = $mediuser->_id;
    $sejour->group_id     = $sender->group_id;

    if ($msg = $sejour->store()) {
      return new CHPrimSanteError($this->_ref_exchange_hpr, "P", "14",  array("P", $this->loop, $this->identifier_patient), "8.24", $msg);
    }

    return $sejour;
  }

  /**
   * merge the patient
   *
   * @param String   $identifier identifier
   * @param String   $tag        tag
   * @param CPatient $patient    patient
   *
   * @return null|CHPrimSanteError
   */
  function mergePatient($identifier, $tag, CPatient $patient) {
    $idex = CIdSante400::getMatch("CPatient", $tag, $identifier["identifier"]);
    if (!$idex->_id) {
      return new CHPrimSanteError($this->_ref_exchange_hpr, "P", "02", array("P", $this->loop, $this->identifier_patient), "8.3.1");
    }

    $idex2 = CIdSante400::getMatch("CPatient", $tag, $identifier["identifier_merge"]);
    if (!$idex2->_id) {
      return new CHPrimSanteError($this->_ref_exchange_hpr, "P", "02", array("P", $this->loop, $this->identifier_patient), "8.3.2");
    }

    $patient->load($idex->object_id);

    $patient2 = new CPatient();
    $patient2->load($idex2->object_id);

    $patientsElimine_array = array($patient2);
    $first_patient_id = $patient->_id;

    // Erreur sur le check du merge
    if ($checkMerge = $patient->checkMerge($patientsElimine_array)) {
      return new CHPrimSanteError($this->_ref_exchange_hpr, "P", "12", array("P", $this->loop, $this->identifier_patient), "8.3.3", $checkMerge);
    }

    $mbPatientElimine_id = $patient2->_id;

    /** @todo mergePlainFields resets the _id */
    $patient->_id = $first_patient_id;

    // Notifier les autres destinataires
    $patient->_eai_initiateur_group_id = $this->_ref_sender->group_id;
    $patient->_merging = CMbArray::pluck($patientsElimine_array, "_id");
    if ($msg = $patient->merge($patientsElimine_array)) {
      return new CHPrimSanteError($this->_ref_exchange_hpr, "P", "12", array("P", $this->loop, $this->identifier_patient), "8.3.3", $msg);
    }

    $patient->_mbPatientElimine_id = $mbPatientElimine_id;

    return null;
  }
}
