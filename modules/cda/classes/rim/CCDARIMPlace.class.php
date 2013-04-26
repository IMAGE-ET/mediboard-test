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
 * CCDARIMPlace Class
 */
class CCDARIMPlace extends CCDARIMEntity {

  /**
   * @var CCDABL
   */
  public $mobileInd;

  /**
   * @var CCDAAD
   */
  public $addr;

  /**
   * @var CCDAED
   */
  public $directionText;

  /**
   * @var CCDAED
   */
  public $positionText;

  /**
   * @var CCDAST
   */
  public $gpsText;

}