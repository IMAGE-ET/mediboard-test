<?php

/**
 * $Id$
 *
 * @category Hprimxml)
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Class CADM
 * Transfert de données d'admission
 */
class CHPrimXMLEventPatient extends CHPrimXML {
  static $evenements = array(
    'enregistrementPatient' => "CHPrimXMLEnregistrementPatient",
    'fusionPatient'         => "CHPrimXMLFusionPatient",
    'venuePatient'          => "CHPrimXMLVenuePatient",
    'fusionVenue'           => "CHPrimXMLFusionVenue",
    'mouvementPatient'      => "CHPrimXMLMouvementPatient",
    'debiteursVenue'        => "CHPrimXMLDebiteursVenue"
  );

  static $documentElements = array(
    'evenementsPatients' => "CHPrimXMLEventPatient"
  );

  /**
   * construct
   */
  function __construct() {
    $this->type = "patients";

    parent::__construct();
  }

  /**
   * Récupération des évènements disponibles
   *
   * @return array
   */
  function getDocumentElements() {
    return self::$documentElements;
  }

  /**
   * Retrieve events list of data format
   *
   * @return array Events list
   */
  function getEvenements() {
    return self::$evenements;
  }

  /**
   * Get event
   *
   * @param string $messagePatient Message
   *
   * @return CHPrimXMLEvenementsPatients|void
   */
  static function getHPrimXMLEvenements($messagePatient) {
    $hprimxmldoc = new CHPrimXMLDocument("patient", CHPrimXMLEvenementsPatients::getVersionEvenementsPatients());
    // Récupération des informations du message XML
    $hprimxmldoc->loadXML($messagePatient);

    $type = $hprimxmldoc->getTypeEvenementPatient();

    $dom_evt = new CHPrimXMLEvenementsPatients();
    if ($type) {
      $dom_evt = new CHPrimXMLEventPatient::$evenements[$type];
    }

    $dom_evt->loadXML($messagePatient);

    return $dom_evt;
  }

  /**
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @return object|null An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
  }
}

