<?php

/**
 * Schémas H'XML
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLSchema
 */
class CHPrimXMLSchema extends CMbXMLSchema {
  /**
   * @see parent::__construct
   */
  function __construct() {
    parent::__construct();
    
    $root = $this->addElement($this, "xsd:schema", null, "http://www.w3.org/2001/XMLSchema");
    $this->addAttribute($root, "xmlns", "http://www.hprim.org/hprimXML");
    $this->addAttribute($root, "xmlns:insee", "http://www.hprim.org/inseeXML");
    $this->addAttribute($root, "targetNamespace", "http://www.hprim.org/hprimXML");
    $this->addAttribute($root, "elementFormDefault", "qualified");
    $this->addAttribute($root, "attributeFormDefault", "unqualified");
  }

  /**
   * @see parent::purgeImportedNamespaces
   */
  function purgeImportedNamespaces() {
    $xpath = new domXPath($this);
    foreach ($xpath->query('//*[@type]') as $node) {
      $matches = null;
      if (preg_match("/insee:(.*)/", $node->getAttribute("type"), $matches)) {
        $node->setAttribute("type", $matches[1]);
      }
    }
  }
}


