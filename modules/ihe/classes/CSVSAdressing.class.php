<?php

/**
 * Adressing Sharing Value Sets - IHE
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CSVSAdressing
 * WSAddressing de SVS
 */
class CSVSAdressing {
  /**
   * Création de l'entête
   *
   * @param String          $nameSpace String
   * @param String          $name      String
   * @param CDMPXmlDocument $xml       CDMPXmlDocument
   *
   * @return SoapHeader
   */
  function createHeaders($nameSpace, $name, $xml) {
    $nameSpace = utf8_encode($nameSpace);
    $name = utf8_encode($name);

    $messageID = new SoapVar($xml->saveXML($xml->documentElement), XSD_ANYXML);
    $header    = new SoapHeader($nameSpace, $name, $messageID);

    return $header;
  }

  /**
   * Création d'une entête WS-Adressing
   *
   * @param bool $return_xml Return the xml
   *
   * @return CSVSMessageXML[]
   */
  static function createWSAddressing($return_xml = false) {
    $xml = new CSVSMessageXML();

    // Action
    $Action = $xml->addElement($xml, "Action", "urn:ihe:iti:2008:RetrieveValueSet", null);
    $Action->setAttributeNS("http://www.w3.org/2003/05/soap-envelope", "e:mustUnderstand", "true");

    // MessageID
    $xml->addElement($xml, "MessageID", "urn:uuid:".CMbSecurity::generateUUID(), null);

    // ReplyTo
    $ReplyTo = $xml->addElement($xml, "ReplyTo", null, null);
    $xml->addElement($ReplyTo, "Address", "http://www.w3.org/2005/08/addressing/anonymous", null);

    // To
    $To = $xml->addElement($xml, "To", "http://valuesetrepository/", null);
    $To->setAttributeNS("http://www.w3.org/2003/05/soap-envelope", "e:mustUnderstand", "true");

    $header   = array();
    if ($return_xml) {
      $header[] = $xml;
    }
    else {
      $header[] = self::createHeaders($saml, $return_xml);
    }
  }
}