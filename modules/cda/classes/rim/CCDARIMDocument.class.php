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
 * CCDARIMDocument Class
 */
class CCDARIMDocument extends CCDARIMContextStructure {

  /**
   * @var CCDATS
   */
  public $copyTime;

  /**
   * @var CCDACE
   */
  public $completionCode;

  /**
   * @var CCDACE
   */
  public $storageCode;

  public $bibliographicDesignationtext = array();

}
