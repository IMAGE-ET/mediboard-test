<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage sherpa
* @version $Revision: 2249 $
* @author Sherpa
*/

/**
 * class for sherpa etablissement
 * - internal synchronisation mechanics
 */
class CSpEtablissement extends CMbObject {  
  // DB key  
  var $sp_etab_id = null;
  
  // Form fields
  var $group_id = null;
  var $increment_year = null;
  var $increment_patient = null;
  
  // Forward references
  var $_ref_group = null;
  
  function CSpEtablissement() {
    $this->CMbObject("sp_etablissement", "sp_etab_id");    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
    
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["group_id"]          = "notNull ref class|CGroups";
    $specs["increment_year"]    = "numchar length|1";
    $specs["increment_patient"] = "num minMax|0|99999";
    return $specs;
  }
   
  function initIncrements() {
    $increment_year = mbTranformTime(null, null, "%Y") % 10;
    $increment_patient = 0;
  }
   
  function loadRefsFwd() {
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function getCurrent() {
    global $g;
    $etab = new CSpEtablissement;
    $etab->group_id = $g;
    $etab->loadMatchingObject();
    
    // Create instance corresponding to current group
    if ($etab->_id) {
      $etab->initIncrements();
      $etab->store();
    }
    
    return $etab;
  }
}

?>