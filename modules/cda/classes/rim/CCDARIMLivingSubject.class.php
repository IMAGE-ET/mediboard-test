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
 * CCDARIMLivingSubject Class
 */
class CCDARIMLivingSubject extends CCDARIMEntity {

  /**
   * @var CCDACE
   */
  public $administrativeGenderCode;

  /**
   * @var CCDATS
   */
  public $birthTime;

  /**
   * @var CCDABL
   */
  public $deceasedInd;

  /**
   * @var CCDATS
   */
  public $deceasedTime;

  /**
   * @var CCDABL
   */
  public $multipleBirthInd;

  /**
   * @var CCDAINT
   */
  public $multipleBirthOrderNumber;

  /**
   * @var CCDABL
   */
  public $organDonorInd;

}
