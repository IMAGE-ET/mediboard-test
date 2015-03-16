<?php

/**
 * Adressing - IHE
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3Adressing
 * WSAddressing
 */
class CHL7v3Adressing {
  /**
   * Création de l'entête
   *
   * @param CHL7v3AdressingMessageXML $dom     HL7v3 XML document
   * @param string                    $eltName Element name
   *
   * @return SoapHeader
   */
  static function createHeaders(CHL7v3AdressingMessageXML $dom, $eltName) {
    $msg = new SoapVar(preg_replace("#^<\?xml[^>]*>#", "", $dom->saveXML()), XSD_ANYXML);

    return new SoapHeader("http://www.w3.org/2005/08/addressing", $eltName, $msg);
  }

  /**
   * Création d'une entête WS-Adressing
   *
   * @param string $action_name Action name
   *
   * @return CSVSMessageXML[]
   */
  static function createWSAddressing($action_name) {
    // Action
    $xml    = new CHL7v3AdressingMessageXML();
    $Action = $xml->addElement($xml, "Action", $action_name);
    $xml->addAttribute($Action, "mustUnderstand", "1");
    $headers[] = self::createHeaders($xml, "Action");

    // MessageID
    $xml = new CHL7v3AdressingMessageXML();
    $xml->addElement($xml, "MessageID", "urn:uuid:".CMbSecurity::generateUUID());
    $headers[] = self::createHeaders($xml, "MessageID");

    // ReplyTo
    $xml     = new CHL7v3AdressingMessageXML();
    $ReplyTo = $xml->addElement($xml, "ReplyTo");
    $xml->addElement($ReplyTo, "Address", "http://www.w3.org/2005/08/addressing/anonymous");
    $headers[] = self::createHeaders($xml, "ReplyTo");

    // To
    $xml = new CHL7v3AdressingMessageXML();
    $To  = $xml->addElement($xml, "To", "http://valuesetrepository/");
    $xml->addAttribute($To, "mustUnderstand", "1");
    $headers[] = self::createHeaders($xml, "To");

    return $headers;
  }
}