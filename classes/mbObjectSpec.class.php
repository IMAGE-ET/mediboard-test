<?php /* $Id: mbFieldSpec.class.php 2134 2007-06-29 12:48:22Z MyttO $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Thomas Despoix
*/

class CMbObjectSpec {
  // Specification fields
  public $incremented         = true;
  public $loggable            = true;
  public $nullifyEmptyStrings = true;
  public $dsn                 = 'std';
  public $table               = null;
  public $key                 = null;
  
  // Derivate fields
  public $ds = null;
  
  /**
   * Initialize derivate fields
   */
  public function init() {
    $this->ds = CSQLDataSource::get($this->dsn);
  }
}

?>
