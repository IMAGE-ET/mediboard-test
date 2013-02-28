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
 * Description
 */
class CCDAthumbnal extends CCDAED {

  
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["reference"] = "CCDATEL max:1";
    $props["thumbnail"] = "CCDAthumbnail prohibited";
    $props["mediaType"] = "CCDACS default:text/plain prohibited";
    $props["language"] = "CCDACS prohibited";
    $props["compression"] = "CCDACompressionAlgorithm prohibited";
    $props["integrityCheck"] = "CCDbin prohibited";
    $props["integrityCheckAlgorithm"] = "CCDAintegrityCheckAlgorithm default:SHA-1 prohibited";
    return $props;
  }
}
