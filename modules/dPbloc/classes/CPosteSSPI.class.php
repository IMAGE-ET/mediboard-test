<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CPosteSSPI extends CMbObject {
  // DB Table Key
  var $poste_sspi_id = null;
  
  // DB References
  var $group_id      = null;
  
  // DB Fields
  var $nom           = null;
  
  // References
  var $_ref_bloc    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'poste_sspi';
    $spec->key   = 'poste_sspi_id';
    
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["group_id"] = "ref class|CGroups notNull";
    $props["nom"]      = "str notNull seekable";
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->nom;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["bloc"] = "CBlocOperatoire poste_sspi_id";
    
    return $backProps;
  }
  
  function loadRefBloc() {
    return $this->_ref_bloc = $this->loadUniqueBackRef("bloc");
  }
  
}
