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
 * CCDARIMAct Class
 */
class CCDARIMAct extends CCDAClasseCda{

  /**
   * @var CCDACS
   */
  public $classCode;

  /**
   * @var CCDACS
   */
  public $moodCode;

  /**
   * @var CCDACD
   */
  public $code;

  /**
   * @var CCDABL
   */
  public $negationInd;

  /**
   * @var CCDAST
   */
  public $derivationExpr;

  /**
   * @var CCDAED
   */
  public $title;

  /**
   * @var CCDAED
   */
  public $text;

  /**
   * @var CCDACS
   */
  public $statusCode;

  /**
   * @var CCDAIVL_TS
   */
  public $effectiveTime;

  /**
   * @var CCDAIVL_TS
   */
  public $activityTime;

  /**
   * @var CCDABL
   */
  public $interruptibleInd;

  /**
   * @var CCDACE
   */
  public $levelCode;

  /**
   * @var CCDABL
   */
  public $independentInd;

  /**
   * @var CCDACE
   */
  public $uncertaintyCode;

  /**
   * @var CCDACE
   */
  public $languageCode;

  public $id                   = array();
  public $priorityCode         = array();
  public $confidentialityCode  = array();
  public $repeatNumber         = array();
  public $reasonCode           = array();

}