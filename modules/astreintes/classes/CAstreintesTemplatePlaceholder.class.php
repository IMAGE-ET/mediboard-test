<?php

/**
 * $Id$
 *  
 * @category Astreintes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Description
 */
class CAstreintesTemplatePlaceholder extends CMbTemplatePlaceholder {
  /**
   * Standard constuctor
   */
  function __construct() {
    parent::__construct("astreintes");
    $this->minitoolbar = "inc_button_astreinte_day";
  }
}
