<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * CCDARIMRole Class
 */
class CCDARIMRole extends CCDAClasseCda{

  /**
   * @var CCDACS
   */
  public $classCode;

  /**
   * @var CCDACE
   */
  public $code;

  /**
   * @var CCDABL
   */
  public $negationInd;

  /**
   * @var CCDACS
   */
  public $statusCode;

  /**
   * @var CCDAED
   */
  public $certificateText;

  /**
   * @var CCDARTO
   */
  public $quantity;

  public $id                  = array();
  public $name                = array();
  public $addr                = array();
  public $telecom             = array();
  public $effectiveTime       = array();
  public $confidentialityCode = array();
  public $positionNumber      = array();

}
