<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage DICOM
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

 /**
  * The Dicom presentation context
  */
class CDicomPresentationContext {
  
  /**
   * The id of the presentation context
   * 
   * @var integer
   */
  public $id;
  
  /**
   * The abstract syntax
   * 
   * @var string
   */
  public $abstract_syntax;
  
  /**
   * The transfer syntax
   * 
   * @var string
   */
  public $transfer_syntax;

  /**
   * The constructor
   *
   * @param integer $id              The id
   *
   * @param string  $abstract_syntax The abstract syntax
   *
   * @param string  $transfer_syntax The transfer syntax
   *
   * @return \CDicomPresentationContext
   */
  function __construct($id, $abstract_syntax, $transfer_syntax = null) {
    $this->id = $id;
    $this->abstract_syntax = $abstract_syntax;
    $this->transfer_syntax = $transfer_syntax;
  }
}