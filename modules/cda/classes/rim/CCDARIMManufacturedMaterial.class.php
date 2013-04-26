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
 * CCDARIMManufacturedMaterial Class
 */
class CCDARIMManufacturedMaterial extends CCDARIMMaterial {

  /**
   * @var CCDAST
   */
  public $lotNumberText;

  public $expirationTime = array();
  public $stabilityTime = array();

}
