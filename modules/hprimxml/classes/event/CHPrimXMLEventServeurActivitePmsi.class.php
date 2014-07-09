<?php

/**
 * $Id$
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLEventServeurActivitePmsi
 *
 */
class CHPrimXMLEventServeurActivitePmsi extends CHPrimXML {
  static $evenements = array(
    'evenementPMSI'                => "CHPrimXMLEvenementsPmsi",
    'evenementServeurActe'         => "CHPrimXMLEvenementsServeurActes",
    'evenementServeurEtatsPatient' => "CHPrimXMLEvenementsServeurEtatsPatient",
    'evenementFraisDivers'         => "CHPrimXMLEvenementsFraisDivers",
    'evenementServeurIntervention' => "CHPrimXMLEvenementsServeurIntervention",
  );

  static $documentElements = array(
    'evenementsServeurActes'       => "CHPrimXMLEventServeurActivitePmsi",
    'evenementsPMSI'               => "CHPrimXMLEventServeurActivitePmsi",
    'evenementsFraisDivers'        => "CHPrimXMLEventServeurActivitePmsi",
    'evenementServeurIntervention' => "CHPrimXMLEventServeurActivitePmsi",
  );

  /**
   * construct
   */
  function __construct() {
    $this->type = "pmsi";

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
   * @see parent::getHPrimXMLEvenements
   */
  static function getHPrimXMLEvenements($messageServeurActivitePmsi) {
    $hprimxmldoc = new CMbXMLDocument();
    $hprimxmldoc->loadXML($messageServeurActivitePmsi);

    $xpath = new CMbXPath($hprimxmldoc);
    $event = $xpath->queryUniqueNode("/*/*[2]");

    $dom_evt = new CHPrimXMLEvenementsServeurActivitePmsi();
    if ($nodeName = $event->nodeName) {
      $dom_evt = new CHPrimXMLEventServeurActivitePmsi::$evenements[$nodeName];
    }

    $dom_evt->loadXML($messageServeurActivitePmsi);

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

