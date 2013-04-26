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
 * CCDARIMObservation Class
 */
class CCDARIMObservation extends CCDARIMAct {

  /**
   * @var CCDAANY
   */
  public $value;

  public $interpretationCode = array();
  public $methodCode         = array();
  public $targetSiteCode     = array();

}
