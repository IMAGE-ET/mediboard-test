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
 * CCDARIMActRelationship Class
 */
class CCDARIMActRelationship extends CCDAClasseCda{

  /**
   * @var CCDACS
   */
  public $typeCode;

  /**
   * @var CCDABL
   */
  public $inversionInd;

  /**
   * @var CCDACS
   */
  public $contextControlCode;

  /**
   * @var CCDABL
   */
  public $contextConductionInd;

  /**
   * @var CCDAINT
   */
  public $sequenceNumber;

  /**
   * @var CCDAINT
   */
  public $priorityNumber;

  /**
   * @var CCDAPQ
   */
  public $pauseQuantity;

  /**
   * @var CCDACS
   */
  public $checkpointcode;

  /**
   * @var CCDACS
   */
  public $splitCode;

  /**
   * @var CCDACS
   */
  public $joinCode;

  /**
   * @var CCDABL
   */
  public $negationInd;

  /**
   * @var CCDACS
   */
  public $conjunctionCode;

  /**
   * @var CCDAST
   */
  public $localVariableName;

  /**
   * @var CCDABL
   */
  public $seperatableInd;

  /**
   * @var CCDACS
   */
  public $subsetCode;

}