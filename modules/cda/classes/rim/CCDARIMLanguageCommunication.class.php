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
 * CCDARIMLanguageCommunication Class
 */
class CCDARIMLanguageCommunication extends CCDAClasseCda {

  /**
   * @var CCDACE
   */
  public $languageCode;

  /**
   * @var CCDACE
   */
  public $modeCode;

  /**
   * @var CCDACE
   */
  public $proficiencyLevelCode;

  /**
   * @var CCDABL
   */
  public $preferenceInd;

}