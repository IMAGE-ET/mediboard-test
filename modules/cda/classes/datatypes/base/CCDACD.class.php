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
 * A concept descriptor represents any kind of concept usually
 * by giving a code defined in a code system.  A concept
 * descriptor can contain the original text or phrase that
 * served as the basis of the coding and one or more
 * translations into different coding systems. A concept
 * descriptor can also contain qualifiers to describe, e.g.,
 * the concept of a "left foot" as a postcoordinated term built
 * from the primary code "FOOT" and the qualifier "LEFT".
 * In exceptional cases, the concept descriptor need not
 * contain a code but only the original text describing
 * that concept.
 */
class CCDACD extends CCDAANY {

  /**
   * The text or phrase used as the basis for the coding.
   * @var CCDAED
   */
  public $originalText;

  /**
   * Specifies additional codes that increase the
   * specificity of the primary code.
   * @var CCDACR
   */
  public $qualifier;

  /**
   * A set of other concept descriptors that translate
   * this concept descriptor into other code systems.
   * @var CCDACD
   */
  public $translation;

  /**
   * The plain code symbol defined by the code system.
   * For example, "784.0" is the code symbol of the ICD-9
   * code "784.0" for headache.
   * @var CCDA_cs
   */
  public $code;

  /**
   * Specifies the code system that defines the code.
   * @var CCDA_uid
   */
  public $codeSystem;

  /**
   * A common name of the coding system.
   * @var CCDA_st
   */
  public $codeSystemName;

  /**
   * If applicable, a version descriptor defined
   * specifically for the given code system.
   * @var CCDA_st
   */
  public $codeSystemVersion;

  /**
   * A name or title for the code, under which the sending
   * system shows the code value to its users.
   * @var CCDA_st
   */
  public $displayName;

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["originalText"] = "CCDAED max:1";
    $props["qualifier"] = "CCDACR";
    $props["translation"] = "CCDACD";
    $props["code"] = "CCDA_cs";
    $props["codeSystem"] = "CCDA_uid";
    $props["codeSystemName"] = "CCDA_st";
    $props["codeSystemVersion"] = "CCDA_st";
    $props["displayName"] = "CCDA_st";
    return $props;
  }
}
