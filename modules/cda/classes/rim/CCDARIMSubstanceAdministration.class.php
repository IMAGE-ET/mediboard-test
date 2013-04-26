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
 * CCDARIMSubstanceAdministration Class
 */
class CCDARIMSubstanceAdministration extends CCDARIMAct {

  /**
   * @var CCDACE
   */
  public $routeCode;

  /**
   * @var CCDACE
   */
  public $administrationUnitCode;

  /**
   * @var CCDAIVL_PQ
   */
  public $doseQuantity;

  /**
   * @var CCDAIVL_PQ
   */
  public $rateQuantity;


  public $approachSiteCode   = array();
  public $doseCheckQuantity  = array();
  public $maxDoseQuantity    = array();

}
