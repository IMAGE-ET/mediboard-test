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
 * Permet de générer le CDA selon les champs générique
 */
class CCDAFactory {
  /** @var String */
  public $root;
  /** @var CMbObject */
  public $mbObject;
  /** @var COperation|CConsultAnesth|CConsultation|CSejour */
  public $targetObject;
  /** @var CPatient */
  public $patient;
  /** @var CUser|CMediusers */
  public $practicien;
  /** @var CCDADomDocument */
  public $dom_cda;
  /** @var  CInteropReceiver */
  public $receiver;

  public $level = 1;
  public $version;
  public $mediaType;
  public $file;
  public $nom;
  public $id_cda;
  public $id_cda_lot;
  public $realm_code;
  public $langage;
  public $confidentialite;
  public $date_creation;
  public $code;
  public $date_author;
  public $industry_code;
  public $healt_care;
  public $service_event = array();
  public $templateId = array();
  public $old_version;
  public $old_id;

  /**
   * Création de la classe en fonction de l'objet passé
   *
   * @param CMbObject $mbObject objet mediboard
   *
   * @return CCDAFactory
   */
  static function factory($mbObject) {
    switch (get_class($mbObject)) {
      case "CFile":
      case "CCompteRendu":
        $class = new CCDAFactoryDocItem($mbObject);
        break;
      default:
        $class = new self($mbObject);
    }

    return $class;
  }

  /**
   * construct
   *
   * @param CMbObject $mbObject Object
   */
  function __construct($mbObject) {
    $this->mbObject = $mbObject;
  }

  /**
   * Extraction des données pour alimenter le CDA
   *
   * @throws CMbException
   * @return void
   */
  function extractData() {
  }

  /**
   * Generation du CDA
   *
   * @return string
   */
  function generateCDA() {
    $this->extractData();
    $document_cda = new CCDADocumentCDA();
    $cda = $document_cda->generateCDA($this);
    $dom = $cda->toXML("ClinicalDocument", "urn:hl7-org:v3");
    $dom->purgeEmptyElements();
    $this->dom_cda = $dom;
    return $dom->saveXML($dom);
  }

  /**
   * Création de templateId
   *
   * @param String $root      String
   * @param String $extension null
   *
   * @return CCDAII
   */
  function createTemplateID($root, $extension = null) {
    $ii = new CCDAII();
    $ii->setRoot($root);
    $ii->setExtension($extension);
    return $ii;
  }
}