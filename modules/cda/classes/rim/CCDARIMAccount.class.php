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
 * CCDARIMAccount Class
 */
class CCDARIMAccount extends CCDARIMAct {

  /**
   * @var CCDAMO
   */
  public $balanceAmt;

  /**
   * @var CCDACE
   */
  public $currencyCode;

  /**
   * @var CCDARTO_QTY_QTY
   */
  public $interestRateQuantity;

  /**
   * @var CCDAIVL_MO
   */
  public $allowedBlanceQuantity;

}
