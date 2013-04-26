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
 * CCDARIMRoleLink Class
 */
class CCDARIMRoleLink extends CCDAClasseCda{

  /**
   * @var CCDACS
   */
  public $typeCode;

  /**
   * @var CCDAINT
   */
  public $priorityNumber;

  /**
   * @var CCDAIVL_TS
   */
  public $effectiveTime;

}