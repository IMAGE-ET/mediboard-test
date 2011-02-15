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

CAppUI::requireSystemClass('mbMetaObject');
class CSourceSMB extends CMbMetaObject{
  // DB Table key
  var $source_smb_id = null;
  
  // DB Fields
  var $name         = null;
  var $host         = null;
  var $user         = null;
  var $password     = null;
  var $workgroup    = null;
  var $printer_name = null;
  var $port         = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_smb';
    $spec->key   = 'source_smb_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["printers"] = "CPrinter object_id";
    return $backProps;
  }
  
  function getProps() {
    $specs = parent::getProps();
    
    $specs["name"]         = "str notNull";
    $specs["object_id"]    = "ref class|CMbObject meta|object_class purgeable show|1";
    $specs["object_class"] = "str class show|0";
    $specs["host"]         = "text notNull";
    $specs["user"]         = "str";
    $specs["password"]     = "password revealable";
    $specs["workgroup"]    = "str";
    $specs["printer_name"] = "str notNull";
    $specs["port"]         = "num";
    return $specs;
  }
  
  function sendDocument($file) {
    $smb = new CSMB;
    $smb->init($this);
    $smb->printFile($file);
  }
}
?>