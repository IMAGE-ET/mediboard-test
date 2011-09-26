<?php

/**
 * MbObject utilities EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAIMbObject
 * MbObject utilities EAI
 */

class CEAIMbObject {
  static function getModifiedFields(CMbObject $mbOject) {
    $mbOject->loadLogs();
    $modified_fields = "";
    if (is_array($mbOject->_ref_current_log->_fields)) {
      foreach ($mbOject->_ref_current_log->_fields as $field) {
        $modified_fields .= "$field \n";
      }
    } 
    
    return $modified_fields;
  }
  
}

?>