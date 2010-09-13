<?php /* $Id: acte.class.php 8867 2010-05-07 07:21:19Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision: 8867 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::loadClass("CActe");

class CFraisDivers extends CActe {
  var $frais_divers_id = null;
  
  // DB fields
  var $type_id     = null;
  var $coefficient = null;
  var $quantite    = null;
  var $facturable  = null;
  
  var $_execution  = null;
  var $_montant    = null;
  
  var $_ref_type   = null;
  
  function getProps() {
    $specs = parent::getProps();
    $specs["type_id"]     = "ref notNull class|CFraisDiversType";
    $specs["coefficient"] = "float notNull default|1";
    $specs["quantite"]    = "num min|0";
    $specs["facturable"]  = "bool notNull default|0";
    
    $specs["_execution"]  = "dateTime";
    $specs["_montant"]    = "currency";
    return $specs;
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "frais_divers";
    $spec->key   = "frais_divers_id";
    return $spec;
  }
  
  function loadRefType(){
    return $this->_ref_type = $this->loadFwdRef("type_id", true);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefType();
    
    $this->_montant = $this->montant_base;
    
    // Vue codée
    $this->_shortview  = $this->quantite > 1 ? "{$this->quantite}x " : "";
    $this->_shortview .= $this->_ref_type->_view;
    
    if ($this->coefficient != 1) {
      $this->_shortview .= $this->coefficient;      
    }
    
    $this->_view = "Frais divers $this->_shortview";
    if ($this->object_class && $this->object_id) {
      $this->_view .= " de $this->object_class-$this->object_id";
    }
  }
  
  function loadExecution() {
    $this->loadTargetObject();
    $this->_ref_object->getActeExecution();
    $this->_execution = $this->_ref_object->_acte_execution;
  }
}
