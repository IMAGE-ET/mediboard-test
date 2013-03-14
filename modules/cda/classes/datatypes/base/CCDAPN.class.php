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
 * A name for a person. A sequence of name parts, such as
 * given name or family name, prefix, suffix, etc. PN differs
 * from EN because the qualifier type cannot include LS
 * (Legal Status).
 */
class CCDAPN extends CCDAEN {

  
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    
    return $props;
  }
}
