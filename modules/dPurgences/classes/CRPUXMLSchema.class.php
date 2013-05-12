<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CRPUXMLSchema extends CMbXMLSchema {
  function __construct() {
    parent::__construct();
    
    $root = $this->addElement($this, "xsd:schema", null, "http://www.w3.org/2001/XMLSchema");
    $this->addAttribute($root, "elementFormDefault", "qualified");
    $this->addAttribute($root, "attributeFormDefault", "unqualified");
  }

  function purgeImportedNamespaces() {
    $xpath = new domXPath($this);

    /** @var DOMElement[] $types */
    $types = $xpath->query('//*[@type]');

    foreach ($types as $node) {
      $matches = null;
      if (preg_match("/insee:(.*)/", $node->getAttribute("type"), $matches)) {
        $node->setAttribute("type", $matches[1]);
      }
    }
  }
}
