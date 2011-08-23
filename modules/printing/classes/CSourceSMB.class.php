<?php

/**
 * Source SMB PRINTING
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSourceSMB
 * Source SMB
 */

CAppUI::requireModuleClass("printing", "CSourcePrinter");

class CSourceSMB extends CSourcePrinter {
  // DB Table key
  var $source_smb_id = null;
  
  // DB Fields
  var $user         = null;
  var $password     = null;
  var $workgroup    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_smb';
    $spec->key   = 'source_smb_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["user"]         = "str";
    $props["password"]     = "password revealable";
    $props["workgroup"]    = "str";
    return $props;
  }
  
  function sendDocument($file) {
    $smb = new CSMB;
    $smb->init($this);
    $smb->printFile($file);
  }
}
?>