<?php

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Classe utilisé par Name
 */
class CXDSLocalizedString {

  public $value;
  public $charset;
  public $lang;

  /**
   * Construction de l'instance
   *
   * @param String $value String
   */
  function __construct($value) {
    $this->value = $value;
    $this->charset = "UTF8";
    $this->lang = "FR";
  }

  /**
   * Génération du xml
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = new CXDSXmlDocument();
    $xml->createLocalized($this->value, $this->charset, $this->lang);

    return $xml;
  }
}
