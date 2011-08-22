<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
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
    foreach ($xpath->query('//*[@type]') as $node) {
      $matches = null;
      if (preg_match("/insee:(.*)/", $node->getAttribute("type"), $matches)) {
        $node->setAttribute("type", $matches[1]);
      }
    }
  }
}

?>
