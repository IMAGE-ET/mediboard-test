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
  public $poste_sspi_id;
  
  // DB References
  public $group_id;
  public $bloc_id;
  
  // DB Fields
  public $nom;
  
  /** @var CBlocOperatoire */
  public $_ref_bloc;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'poste_sspi';
    $spec->key   = 'poste_sspi_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["bloc_id"]  = "ref class|CBlocOperatoire";
    $props["group_id"] = "ref class|CGroups notNull";
    $props["nom"]      = "str notNull seekable";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["operations"] = "COperation poste_sspi_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->nom;
  }

  /**
   * @return CBlocOperatoire
   */
  function loadRefBloc() {
    return $this->_ref_bloc = $this->loadFwdRef("bloc_id");
  }
}
