<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
 
/**
 * Coded data, consists of a code, display name, code system,
 * and original text. Used when a single code value must be sent.
 */
class CCDACV extends CCDACE {

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["translation"] = "CCDACD xml|element prohibited";
    return $props;
  }
}
