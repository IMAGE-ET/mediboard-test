<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CCategoryProduitPrescriptible extends CMbObject {
  // DB fields
  var $nom         = null;
  var $description = null;
  var $group_id    = null;
  
  // Form fields
  var $_count_elements = null;
  
  function getProps() {
  	$props = parent::getProps();
    $props["nom"]         = "str notNull seekable";
    $props["description"] = "text seekable";
    $props["group_id"]    = "ref notNull class|CGroups";
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
}

?>