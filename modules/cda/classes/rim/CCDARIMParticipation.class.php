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
 * CCDARIMParticipation Class
 */
class CCDARIMParticipation extends CCDAClasseCda {

  /**
   * @var CCDACS
   */
  public $typeCode;

  /**
   * @var CCDACD
   */
  public $functionCode;

  /**
   * @var CCDACS
   */
  public $contextControlCode;

  /**
   * @var CCDAINT
   */
  public $sequenceNumber;

  /**
   * @var CCDABL
   */
  public $negationInd;

  /**
   * @var CCDAED
   */
  public $noteText;

  /**
   * @var CCDACE
   */
  public $modeCode;

  /**
   * @var CCDACE
   */
  public $awarenessCode;

  /**
   * @var CCDACE
   */
  public $signatureCode;

  /**
   * @var CCDAED
   */
  public $signatureText;

  /**
   * @var CCDABL
   */
  public $performInd;

  /**
   * @var CCDACE
   */
  public $substitutionConditionCode;

  public $time = array();

}