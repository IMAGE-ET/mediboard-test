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
 * Classe xml pour les jeux de valeurs
 */
class CXDSXmlJvDocument extends DOMDocument {

  function __construct() {
    parent::__construct();
    $this->formatOutput = true;
    $this->appendChild($this->createElement("jeuxValeurs"));
  }

  function appendLine($oid, $id, $name) {
    $oid  = utf8_encode($oid);
    $id   = utf8_encode($id);
    $name = utf8_encode(trim($name));
    $element = $this->createElement("line");
    $element->setAttribute("id", $id);
    $element->setAttribute("oid", $oid);
    $element->setAttribute("name", $name);
    $this->documentElement->appendChild($element);
  }
}
