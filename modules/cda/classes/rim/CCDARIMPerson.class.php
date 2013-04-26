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
 * Person class
 */
class CCDARIMPerson extends CCDARIMLivingSubject {

  /**
   * @var CCDACE
   */
  public $maritalStatusCode;

  /**
   * @var CCDACE
   */
  public $educationLevelCode;

  /**
   * @var CCDACE
   */
  public $livingArrangementCode;

  /**
   * @var CCDACE
   */
  public $religiousAffiliationCode;

  public $addr            = array();
  public $raceCode        = array();
  public $disabilityCode  = array();
  public $ethnicGroupCode = array();

}
