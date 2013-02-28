<?php

/**
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
 
/**
 * Coded data in its simplest form, consists of a code.
 * The code system and code system version is fixed by
 * the context in which the CS value occurs. CS is used
 * for coded attributes that have a single HL7-defined
 * value set.
 */
class CCDA_cs {

  public $value;
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = array();
    $props["value"] = "str pattern:[^\\s]+";
    return $props;
  }
}
