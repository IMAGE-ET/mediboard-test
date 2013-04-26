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
 * CCDARIMPatientEncounter Class
 */
class CCDARIMPatientEncounter extends CCDARIMAct {

  /**
   * @var CCDABL
   */
  public $preAdmitTestInd;

  /**
   * @var CCDACE
   */
  public $admissionreferralSourceCode;

  /**
   * @var CCDAPQ
   */
  public $lengthOfStayQuantity;

  /**
   * @var CCDACE
   */
  public $dischargeDispositionCode;

  public $specialCourtesiesCode  = array();
  public $specialArrangementCode = array();

}
