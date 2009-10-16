<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLSchema extends CMbXMLSchema {
  function __construct() {
    parent::__construct();
    
    $root = $this->addElement($this, "xsd:schema", null, "http://www.w3.org/2001/XMLSchema");
    $this->addAttribute($root, "xmlns", "http://www.hprim.org/hprimXML");
    $this->addAttribute($root, "xmlns:insee", "http://www.hprim.org/inseeXML");
    $this->addAttribute($root, "targetNamespace", "http://www.hprim.org/hprimXML");
    $this->addAttribute($root, "elementFormDefault", "qualified");
    $this->addAttribute($root, "attributeFormDefault", "unqualified");
  }

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

?>
