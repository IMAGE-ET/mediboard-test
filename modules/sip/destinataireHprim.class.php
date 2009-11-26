<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CDestinataireHprim extends CMbObject {
  // DB Table key
  var $dest_hprim_id  = null;
  
  // DB Fields
  var $nom      = null;
  var $group_id = null;
  var $type     = null;
  var $url      = null;
  var $username = null;
  var $password = null;
  var $actif    = null;
  
  // Forward references
  var $_ref_group = null;
  
  // Form fields
  var $_tag       = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'destinataire_hprim';
    $spec->key   = 'dest_hprim_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]      = "str notNull";
    $specs["group_id"] = "ref notNull class|CGroups";
    $specs["type"]     = "enum notNull list|cip|sip default|cip";
    $specs["url"]      = "text notNull";
    $specs["username"] = "str notNull";
    $specs["password"] = "password";
    $specs["actif"]    = "bool notNull";
    
    $specs["_tag"]     = "str";
    return $specs;
  }
  
  function loadRefsFwd() {
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_tag = "$this->nom group:$this->group_id";
  }
}
?>