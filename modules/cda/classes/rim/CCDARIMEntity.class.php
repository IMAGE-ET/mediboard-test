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
 * CCDARIMEntity Class
 */
class CCDARIMEntity extends CCDAClasseCda{

  /**
   * @var CCDACS
   */
  public $classCode;

  /**
   * @var CCDACS
   */
  public $determinerCode;

  /**
   * @var CCDACE
   */
  public $code;

  /**
   * @var CCDAED
   */
  public $desc;

  /**
   * @var CCDACS
   */
  public $statusCode;

  /**
   * @var CCDACE
   */
  public $riskCode;

  /**
   * @var CCDACE
   */
  public $handlingCode;

  public $id            = array();
  public $quantity      = array();
  public $name          = array();
  public $existenceTime = array();
  public $telecom       = array();

}