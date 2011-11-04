<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CIndiceCout extends CMbObject {
  
  // DB Table key
  var $indice_cout_id  = null;
  
  // DB Fields
  var $nb       = null;
  var $ressource_soin_id = null;
  var $element_prescription_id = null;
  
  // Ref Fields
  var $_ref_ressource_soin = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'indice_cout';
    $spec->key   = 'indice_cout_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["nb"]                      = "num notNull";
    $specs["ressource_soin_id"]       = "ref class|CRessourceSoin notNull";
    $specs["element_prescription_id"] = "ref class|CElementPrescription notNull";
    
    return $specs;
  }
  
  function loadRefRessourceSoin() {
    $this->_ref_ressource_soin = new CRessourceSoin;
    return $this->_ref_ressource_soin->load($this->ressource_soin_id);
  }
}
?>