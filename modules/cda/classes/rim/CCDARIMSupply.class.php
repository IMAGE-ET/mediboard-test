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
 * CCDARIMSupply class
 */
class CCDARIMSupply extends CCDARIMAct {

  /**
   * @var CCDAPQ
   */
  public $quantity;

  /**
   * @var CCDAIVL_TS
   */
  public $expectedUseTime;

}
