<?php /* $Id: CPresctationJournaliere.class.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrestationJournaliere extends CMbObject{
  // DB Table key
  var $prestation_journaliere_id = null;
  
  // DB Fields
  var $nom      = null;
  var $group_id = null;
  var $desire  = null;
  
  // Form fields
  var $_count_items = 0;
  var $_ref_items   = 0;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "prestation_journaliere";
    $spec->key   = "prestation_journaliere_id";
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]       = "str notNull";
    $specs["group_id"]  = "ref notNull class|CGroups";
    $specs["desire"]    = "bool default|0";
    
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
    $prestation = new CPrestationJournaliere();
    $prestation->group_id = CGroups::loadCurrent()->_id;
    return $prestation->loadMatchingList("nom");
  }
  
  static function countCurrentList() {
    $prestation = new CPrestationJournaliere();
    $prestation->group_id = CGroups::loadCurrent()->_id;
    return $prestation->countMatchingList();
  }
}