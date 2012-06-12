<?php /* $Id: CItemPrestation.class.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CItemPrestation extends CMbMetaObject{
  // DB Table key
  var $item_prestation_id = null;
  
  // DB Fields
  var $nom                = null;
  var $rank               = null;
  
  // Ref field
  var $_ref_object        = null;
  
  // Form field
  var $_quantite          = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "item_prestation";
    $spec->key   = "item_prestation_id";
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]          = "str notNull seekable";
    /*$specs["object_id"]    = "ref notNull class|CMbObject meta|object_class";*/
    $specs["object_class"] = "enum list|CPrestationPonctuelle|CPrestationJournaliere";
    $specs["rank"]         = "num pos default|1";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["liaisons_souhaits"] = "CItemLiaison item_souhait_id";
    $backProps["liaisons_realises"] = "CItemLiaison item_realise_id";
    $backProps["liaisons_lits"]     = "CLitLiaisonItem item_prestation_id";
    return $backProps;
  }
  
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  function loadRefObject(){
    $this->_ref_object = new $this->object_class;
    return $this->_ref_object = $this->_ref_object->getCached($this->object_id);
  }
}