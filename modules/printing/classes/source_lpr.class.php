<?php

/**
 * Source LPR PRINTING
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSourceLPR 
 * Source LPR
 */

CAppUI::requireSystemClass('mbMetaObject');
class CSourceLPR extends CMbMetaObject {
  // DB Table key
  var $source_lpr_id = null;
  
  // DB Fields
  var $name         = null;
  var $host         = null;
  var $user         = null;
  var $printer_name  = null;
  var $port          = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_lpr';
    $spec->key   = 'source_lpr_id';
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
    $specs["printer_name"] = "str";
    $specs["port"]         = "num";
    return $specs;
  }
  
  function sendDocument($file) {
    $lpr = new CLPR;
    $lpr->init($this);
    $lpr->printFile($file);
  }
}
?>