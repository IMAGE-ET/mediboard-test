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
 * CCDARIMContainer Class
 */
class CCDARIMContainer extends CCDARIMManufacturedMaterial {

  /**
   * @var CCDAPQ
   */
  public $capacityQuantity;

  /**
   * @var CCDAPQ
   */
  public $heightquantity;

  /**
   * @var CCDAPQ
   */
  public $diameterQuantity;

  /**
   * @var CCDACE
   */
  public $capTypeCode;

  /**
   * @var CCDACE
   */
  public $seperatortypeCode;

  /**
   * @var CCDAPQ
   */
  public $barrierDeltaQuantity;

  /**
   * @var CCDAPQ
   */
  public $bottomDeltaQuantity;
}
