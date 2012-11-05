<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
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
  var $id = null;
  
  /**
   * The abstract syntax
   * 
   * @var string
   */
  var $abstract_syntax = null;
  
  /**
   * The transfer syntax
   * 
   * @var string
   */
  var $transfer_syntax = null;
  
  /**
   * The constructor
   * 
   * @param integer $id              The id
   * 
   * @param string  $abstract_syntax The abstract syntax
   * 
   * @param string  $transfer_syntax The transfer syntax
   * 
   * @return null
   */
  function __construct($id, $abstract_syntax, $transfer_syntax = null) {
    $this->id = $id;
    $this->abstract_syntax = $abstract_syntax;
    $this->transfer_syntax = $transfer_syntax;
  }
}
?>