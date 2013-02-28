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
 * Data that is primarily intended for human interpretation
 * or for further machine processing is outside the scope of
 * HL7. This includes unformatted or formatted written language,
 * multimedia data, or structured information as defined by a
 * different standard (e.g., XML-signatures.)  Instead of the
 * data itself, an ED may contain
 * only a reference (see TEL.) Note
 * that the ST data type is a
 * specialization of the ED data type
 * when the ED media type is text/plain.
 */
class CCDAED extends CCDABIN {

  /**
   * A telecommunication address (TEL), such as a URL
   * for HTTP or FTP, which will resolve to precisely
   * the same binary data that could as well have been
   * provided as inline data.
   * @var CCDATEL
   */
  public $reference;

  /**
   * @var CCDAthumbnail
   */
  public $thumbnail;

  /**
   * Identifies the type of the encapsulated data and
   * identifies a method to interpret or render the data.
   * @var CCDACS
   */
  public $mediaType;

  /**
   * For character based information the language property
   * specifies the human language of the text.
   * @var CCDACS
   */
  public $language;

  /**
   * Indicates whether the raw byte data is compressed,
   * and what compression algorithm was used.
   * @var CCDACompressionAlgorithm
   */
  public $compression;

  /**
   * The integrity check is a short binary value representing
   * a cryptographically strong checksum that is calculated
   * over the binary data. The purpose of this property, when
   * communicated with a reference is for anyone to validate
   * later whether the reference still resolved to the same
   * data that the reference resolved to when the encapsulated
   * data value with reference was created.
   * @var CCDAbin
   */
  public $integrityCheck;

  /**
   * Specifies the algorithm used to compute the
   * integrityCheck value.
   * @var CCDAIntegrityCheckAlgorithm
   */
  public $integrityCheckAlgorithm;

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["reference"] = "CCDATEL max:1";
    $props["thumbnail"] = "CCDAthumbnail max:1";
    $props["mediaType"] = "CCDACS default:text/plain";
    $props["language"] = "CCDACS";
    $props["compression"] = "CCDACompressionAlgorithm";
    $props["integrityCheck"] = "CCDbin";
    $props["integrityCheckAlgorithm"] = "CCDAintegrityCheckAlgorithm default:SHA-1";
    return $props;
  }
}
