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
 * CCDARIMPublicHealthCase Class
 */
class CCDARIMPublicHealthCase extends CCDARIMObservation {

  /**
   * @var CCDACE
   */
  public $detectionMethodCode;

  /**
   * @var CCDACE
   */
  public $transmissionModeCode;

  /**
   * @var CCDACE
   */
  public $diseaseImportedCode;

}
