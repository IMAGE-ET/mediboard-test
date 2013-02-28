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
 * Binary data is a raw block of bits. Binary data is a
 * protected type that MUST not be used outside the data
 * type specification.
 */
abstract class CCDABIN extends CCDAANY {

  /**
   * Specifies the representation of the binary data that
   * is the content of the binary data value.
   * @var CCDABinaryDataEncoding
   */
  public $representation;
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["representation"] = "CCDABinaryDataEncoding default:TXT";
    return $props;
  }
}
