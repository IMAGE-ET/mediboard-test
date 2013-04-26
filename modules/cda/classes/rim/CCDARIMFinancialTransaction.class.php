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
 * CCDARIMFinancialTransaction Class
 */
class CCDARIMFinancialTransaction extends CCDARIMAct {

  /**
   * @var CCDAMO
   */
  public $amt;

  /**
   * @var CCDAREAL
   */
  public $creditExchangeRateQuantity;

  /**
   * @var CCDAREAL
   */
  public $debitExchangeRateQuantity;

}
