<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Classe regroupant les fonctions de type Act
 */
class CCDAActCDA extends CCDADocumentCDA {

  /**
   * Création d'un clinicalDocument
   *
   * @return CCDAPOCD_MT000040_ClinicalDocument
   */
  function setClinicalDocument() {
    $factory         = self::$cda_factory;
    $participation   = parent::$participation;
    $actRelationship = parent::$actRelationship;

    //déclaration du document
    $clinicaldocument = new CCDAPOCD_MT000040_ClinicalDocument();

    /**
     * Création de l'entête
     */

    //Création de l'Id du document
    $ii = new CCDAII();
    $ii->setRoot($factory->id_cda);
    $clinicaldocument->setId($ii);

    //création du typeId
    $clinicaldocument->setTypeId();

    //Ajout du realmCode FR
    $cs = new CCDACS();
    $cs->setCode($factory->realm_code);
    $clinicaldocument->appendRealmCode($cs);

    //Ajout du code langage fr-FR
    $cs = new CCDACS();
    $cs->setCode($factory->langage);
    $clinicaldocument->setLanguageCode($cs);

    //Ajout de la confidentialité du document
    $confidentialite = $factory->confidentialite;
    $ce = new CCDACE();
    $ce->setCode($confidentialite["code"]);
    $ce->setCodeSystem($confidentialite["codeSystem"]);
    $ce->setDisplayName($confidentialite["displayName"]);

    $clinicaldocument->setConfidentialityCode($ce);

    //Ajout de la date de création du document
    $ts = new CCDATS();
    $ts->setValue($this->getTimeToUtc($factory->date_creation));
    $clinicaldocument->setEffectiveTime($ts);

    //Ajout du numéro de version
    $int = new CCDAINT();
    $int->setValue($factory->version);
    $clinicaldocument->setVersionNumber($int);

    //Ajout de l'identifiant du lot
    $ii = new CCDAII();
    $ii->setRoot($factory->id_cda_lot);
    $clinicaldocument->setSetId($ii);

    //Ajout du nom du document
    $st = new CCDAST();
    $st->setData($factory->nom);
    $clinicaldocument->setTitle($st);

    //Ajout du code du document (Jeux de valeurs)
    $ce = new CCDACE();
    $code = $factory->code;
    $ce->setCode($code["code"]);
    $ce->setCodeSystem($code["codeSystem"]);
    $ce->setDisplayName($code["displayName"]);
    $clinicaldocument->setCode($ce);

    /**
     * Déclaration Template
     */
    //conformité HL7
    foreach ($factory->templateId as $_templateId) {
      $clinicaldocument->appendTemplateId($_templateId);
    }

    /**
     * Création des éléments obligatoire constituant le document
     */
    $clinicaldocument->appendRecordTarget($participation->setRecordTarget());
    $clinicaldocument->setCustodian($participation->setCustodian());
    $clinicaldocument->appendAuthor($participation->setAuthor());
    $clinicaldocument->setLegalAuthenticator($participation->setLegalAuthenticator());
    $clinicaldocument->appendDocumentationOf($actRelationship->setDocumentationOF());
    $clinicaldocument->setComponentOf($actRelationship->setComponentOf());

    /**
     * Création du corp du document
     */
    $clinicaldocument->setComponent($actRelationship->setComponent2());
    return $clinicaldocument;
  }

  /**
   * Création d'un corps non structuré
   *
   * @return CCDAPOCD_MT000040_NonXMLBody
   */
  function setNonXMLBody() {
    $file      = self::$cda_factory->file;
    $mediaType = self::$cda_factory->mediaType;
    $nonXMLBody = new CCDAPOCD_MT000040_NonXMLBody();

    $ed = new CCDAED();
    $ed->setMediaType($mediaType);
    $ed->setRepresentation("B64");
    $ed->setData(base64_encode(file_get_contents($file->_file_path)));

    $nonXMLBody->setText($ed);
    return $nonXMLBody;
  }

  /**
   * Création encompassingEncounter
   *
   * @return CCDAPOCD_MT000040_EncompassingEncounter
   */
  function setEncompassingEncounter() {
    $encompassingEncounter = new CCDAPOCD_MT000040_EncompassingEncounter();
    /** @var CSejour|COperation|CConsultation $object CSejour*/
    $object = self::$cda_factory->targetObject;
    $ivl = "";
    switch (get_class($object)) {
      case "CSejour":
        $low = $object->entree_reelle;
        if (!$low) {
          $low = $object->entree_prevue;
        }

        $high = $object->sortie_reelle;
        if (!$high) {
          $high = $object->sortie_prevue;
        }

        $ivl = $this->createIvlTs($low, $high);

        break;
      case "COperation":
        $ivl = $this->createIvlTs($object->debut_op, $object->fin_op);
        $encompassingEncounter->setEffectiveTime($ivl);

        break;
      case "CConsultation":
        $object->loadRefPlageConsult();
        $ivl = $this->createIvlTs($object->_datetime, $object->_date_fin, true);
        break;
    }
    $encompassingEncounter->setEffectiveTime($ivl);

    $encompassingEncounter->setLocation(parent::$participation->setLocation());

    return $encompassingEncounter;
  }

  /**
   * Création de service event
   *
   * @return CCDAPOCD_MT000040_ServiceEvent
   */
  function setServiceEvent() {
    $service_event = self::$cda_factory->service_event;

    $serviceEvent = new CCDAPOCD_MT000040_ServiceEvent();
    $ce           = new CCDACE();
    $time_start   = $service_event["time_start"];
    $time_stop    = $service_event["time_stop"];
    $ivl = parent::createIvlTs($time_start, $time_stop);
    $serviceEvent->setEffectiveTime($ivl);
    if ($service_event["nullflavor"]) {
      $ce->setNullFlavor($service_event["nullflavor"]);
    }
    else {
      $ce->setCode($service_event["code"]);
      $ce->setCodeSystem($service_event["oid"]);
    }
    $serviceEvent->appendPerformer(parent::$participation->setPerformer($service_event["executant"]));
    $serviceEvent->setCode($ce);

    return $serviceEvent;
  }
}