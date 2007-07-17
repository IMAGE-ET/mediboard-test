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
    
  /**
   * Map the sherpa object form a mediboard 
   */
  function mapFrom(CMbObject &$mbObject) {
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->incremented = false;
    $spec->loggable = true;
    return $spec;
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