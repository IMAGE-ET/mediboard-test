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
 * CCDARIMEmployee Class
 */
class CCDARIMEmployee extends CCDARIMRole {

  /**
   * @var CCDACE
   */
  public $jobCode;

  /**
   * @var CCDASC
   */
  public $jobTitleName;

  /**
   * @var CCDACE
   */
  public $jobClassCode;

  /**
   * @var CCDACE
   */
  public $occupationCode;

  /**
   * @var CCDACE
   */
  public $salaryTypeCode;

  /**
   * @var CCDAMO
   */
  public $salaryQuantity;

  /**
   * @var CCDAED
   */
  public $hazardExposureText;

  /**
   * @var CCDAED
   */
  public $protectiveEquipementText;
}
