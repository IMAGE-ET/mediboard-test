<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CDestinataireInterop extends CMbObject {
  // DB Fields
  var $nom         = null;
  var $libelle     = null;
  var $group_id    = null;
  var $actif       = null;
  var $message     = null;
  
  // Forward references
  var $_ref_group             = null;
  var $_ref_exchanges_sources = null;
  
  // Form fields
  var $_tag_patient  = null;
  var $_tag_sejour   = null;
  var $_tag_mediuser = null;
  var $_tag_service  = null;
  var $_type_echange = null;
  
  function getSpec() {
    $spec = parent::getSpec();

    $spec->messages = array();
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["nom"]         = "str notNull";
    $props["libelle"]     = "str";
    $props["group_id"]    = "ref notNull class|CGroups autocomplete|text";
    $props["actif"]       = "bool notNull";

    $props["_tag_patient"]  = "str";
    $props["_tag_sejour"]   = "str";
    $props["_tag_mediuser"] = "str";
    $props["_tag_service"]  = "str";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    return $backProps;
  }
    
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", 1);
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->libelle ? $this->libelle : $this->nom;
    $this->_type_echange = $this->_class_name;
  }
}

?>
