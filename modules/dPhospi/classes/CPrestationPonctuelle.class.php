<?php /* $Id: CPresctationJournaliere.class.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrestationPonctuelle extends CMbObject{
  // DB Table key
  var $prestation_ponctuelle_id = null;
  
  // DB fields
  var $nom      = null;
  var $group_id = null;
  
  // Form fields
  var $_count_items = 0;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "prestation_ponctuelle";
    $spec->key   = "prestation_ponctuelle_id";
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]       = "str notNull seekable";
    $specs["group_id"]  = "ref notNull class|CGroups";
    
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["items"] = "CItemPrestation object_id";
   
    return $backProps;
  }
  
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  static function loadCurrentList() {
    $prestation = new CPrestationPonctuelle;
    $prestation->group_id = CGroups::loadCurrent()->_id;
    return $prestation->loadMatchingList("nom");
  }
  
  static function countCurrentList() {
    $prestation = new CPrestationPonctuelle;
    $prestation->group_id = CGroups::loadCurrent()->_id;
    return $prestation->countMatchingList();
  }
}