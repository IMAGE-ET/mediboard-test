<?php

/**
 * Source Printer PRINTING
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSourcePrinter
 * Source Printer
 */

class CSourcePrinter extends CMbObject{
  
  // DB Fields
  var $name         = null;
  var $host         = null;
  var $port         = null;
  var $printer_name = null;

  function getProps() {
    $props = parent::getProps();
    
    $props["name"]         = "str notNull";
    $props["host"]         = "text notNull";
    $props["port"]         = "num";
    $props["printer_name"] = "str notNull";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["printers"] = "CPrinter object_id";
    return $backProps;
  }
  
  function sendDocument($file) { }
}
?>