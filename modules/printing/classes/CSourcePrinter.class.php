<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Printing
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Source Printer
 */
class CSourcePrinter extends CMbObject{
  // DB Fields
  public $name;
  public $host;
  public $port;
  public $printer_name;

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["name"]         = "str notNull";
    $props["host"]         = "text notNull";
    $props["port"]         = "num";
    $props["printer_name"] = "str notNull";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["printers"] = "CPrinter object_id";

    return $backProps;
  }

  /**
   * Send a file to the printer
   *
   * @param CFile $file The file to print
   *
   * @return void
   */
  function sendDocument(CFile $file) {

  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->name;
  }
}
