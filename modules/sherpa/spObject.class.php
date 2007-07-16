<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage sherpa
* @version $Revision: 2249 $
* @author Sherpa
*/

/**
 * Abstract class for sherpa objects
 * - base association
 */
class CSpObject extends CMbObject {  
  
  // Form fields
  var $_sp_id = null;
  
  /**
   * Produce sherpa id form mediboard object
   */
  function makeSpIdFrom(CMbObject &$mbObject) {
    // Dummy id computation
    $this->_sp_id = $mbObject->_id + 10;
  }
  
  /**
   * Map the sherpa object form a mediboard 
   */
  function mapFrom(CMbObject &$mbObject) {
  }
  
  function getBackRefs() {
    return array();
  }
 
  function getspecs() {
    return array();
  }
   
  function getSeeks() {
    return array();
  }

  function getHelpedFields(){
    return array();
  }
}

?>