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
    $docItem = parent::$docItem;
    $root = parent::$root;
    $participation = parent::$participation;
    $actRelationship = parent::$actRelationship;

    //déclaration du document
    $clinicaldocument = new CCDAPOCD_MT000040_ClinicalDocument();

    /**
     * Création de l'entête
     */

    //Création de l'Id du document
    $ii = new CCDAII();
    $ii->setRoot($root);
    $ii->setExtension("$docItem->_id/$docItem->version");
    $clinicaldocument->setId($ii);

    //création du typeId
    $clinicaldocument->setTypeId();

    //Ajout du realmCode FR
    $cs = new CCDACS();
    $cs->setCode("FR");
    $clinicaldocument->appendRealmCode($cs);

    //Ajout du code langage fr-FR
    $cs = new CCDACS();
    $cs->setCode("fr-FR");
    $clinicaldocument->setLanguageCode($cs);

    //Ajout de la confidentialité du document
    $ce = new CCDACE();
    $ce->setCode("N");
    $ce->setCodeSystem("2.16.840.1.113883.5.25");
    if ($docItem->private) {
      $ce->setCode("R");
    }
    $clinicaldocument->setConfidentialityCode($ce);

    //Ajout de la date de création du document
    $ts = new CCDATS();
    $ts->setValue($this->getTimeToUtc($docItem->_ref_last_log->date));
    $clinicaldocument->setEffectiveTime($ts);

    //Ajout du numéro de version
    $int = new CCDAINT();
    $int->setValue(intval($docItem->version));
    $clinicaldocument->setVersionNumber($int);

    //Ajout de l'identifiant du lot
    $ii = new CCDAII();
    $ii->setRoot($root);
    $ii->setExtension($docItem->_id);
    $clinicaldocument->setSetId($ii);

    //Ajout du nom du document
    $st = new CCDAST();
    $st->setData($docItem->nom);
    $clinicaldocument->setTitle($st);

    //Ajout du code du document (Jeux de valeurs)
    $ce = new CCDACE();
    $ce->setCode("test");
    $clinicaldocument->setCode($ce);

    /**
     * Déclaration Template
     */
    //conformité HL7
    $template = $this->createTemplateID("2.16.840.1.113883.2.8.2.1", "HL7 France");
    $clinicaldocument->appendTemplateId($template);

    //Conformité CI-SIS
    $template = $this->createTemplateID("1.2.250.1.213.1.1.1.1", "CI-SIS");
    $clinicaldocument->appendTemplateId($template);

    //Confirmité IHE XSD-SD => contenu non structuré
    $template = $this->createTemplateID("1.3.6.1.4.1.19376.1.2.20", "IHE XDS-SD");
    $clinicaldocument->appendTemplateId($template);

    /**
     * Création des éléments obligatoire constituant le document
     */
    $clinicaldocument->appendRecordTarget($participation->setRecordTarget());
    $clinicaldocument->setCustodian($participation->setCustodian());
    $clinicaldocument->appendAuthor($participation->setAuthor());
    $clinicaldocument->setLegalAuthenticator($participation->setLegalAuthenticator());
    $this->setDocumentationOF($clinicaldocument);
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
    $docItem = parent::$docItem;
    $nonXMLBody = new CCDAPOCD_MT000040_NonXMLBody();

    $ed = new CCDAED();
    $ed->setMediaType("application/pdf");
    $ed->setRepresentation("B64");
    $docItem->makePDFpreview(1);
    $file = $docItem->_ref_file;
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

    $docItem = parent::$docItem;
    $object = $docItem->_ref_object;
    $ivl = "";
    $praticien = "";
    switch (get_class($object)) {
      case "CSejour":
        /** @var CSejour $object CSejour*/
        $praticien = $object->loadRefPraticien();
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
        /** @var COperation $object COperation*/
        $praticien = $object->loadRefChir();
        $ivl = $this->createIvlTs($object->debut_op, $object->fin_op);
        $encompassingEncounter->setEffectiveTime($ivl);

        break;
      case "CConsultation":
        /** @var CConsultation $object CConsultation*/
        $object->loadRefPlageConsult();
        $praticien = $object->loadRefPraticien();
        $ivl = $this->createIvlTs($object->_datetime, $object->_date_fin, true);
        break;
    }
    $encompassingEncounter->setEffectiveTime($ivl);

    $encompassingEncounter->setLocation(parent::$participation->setLocation($praticien));

    return $encompassingEncounter;
  }
}