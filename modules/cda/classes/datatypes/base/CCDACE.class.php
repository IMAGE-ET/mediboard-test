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
 * Coded data, consists of a coded value (CV)
 * and, optionally, coded value(s) from other coding systems
 * that identify the same concept. Used when alternative
 * codes may exist.
 */
class CCDACE extends CCDACD {

  
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["originalText"] = "CCDAED max:1";
    $props["qualifier"] = "CCDACR prohibited";
    $props["translation"] = "CCDACD";
    $props["code"] = "CCDA_cs";
    $props["codeSystem"] = "CCDA_uid";
    $props["codeSystemName"] = "CCDA_st";
    $props["codeSystemVersion"] = "CCDA_st";
    $props["displayName"] = "CCDA_st";
    return $props;
  }
}
