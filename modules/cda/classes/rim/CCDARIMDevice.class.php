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
 * CCDARIMDevice Class
 */
class CCDARIMDevice extends CCDARIMManufacturedMaterial {

  /**
   * @var CCDASC
   */
  public $manufacturerModelName;

  /**
   * @var CCDASC
   */
  public $softwareName;

  /**
   * @var CCDACE
   */
  public $localRemoteControlStateCode;

  /**
   * @var CCDACE
   */
  public $alertLevelCode;

  /**
   * @var CCDATS
   */
  public $lastCalibrationTime;

}
