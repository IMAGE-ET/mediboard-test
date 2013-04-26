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
 * CCDARIMInvoiceElement Class
 */
class CCDARIMInvoiceElement extends CCDARIMAct {

  /**
   * @var CCDAMO
   */
  public $netAmt;

  /**
   * @var CCDAREAL
   */
  public $factorNumber;

  /**
   * @var CCDAREAL
   */
  public $pointsNumber;

  /**
   * @var CCDARTO_QTY_QTY
   */
  public $unitQuantity;

  /**
   * @var CCDARTO_QTY_QTY
   */
  public $unitPriceAmt;


  public $modifierCode = array();

}
