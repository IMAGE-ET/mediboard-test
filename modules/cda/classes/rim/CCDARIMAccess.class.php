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
 * CCDARIMAccess Class
 */
class CCDARIMAccess extends CCDARIMRole {

  /**
   * @var CCDACD
   */
  public $approachSiteCode;

  /**
   * @var CCDACD
   */
  public $targetSiteCode;

  /**
   * @var CCDAPQ
   */
  public $gaugeQuantity;
}
