<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Config hprim sante
 */
class CHPrimSanteConfig extends CExchangeDataFormatConfig {

  static $config_fields = array(
    // Format
    "encoding",
    "strict_segment_terminator",
    "segment_terminator",

    //handle
    "action"
  );

  /** @var integer Primary key */
  public $hprimsante_config_id;

  // Format
  public $encoding;
  public $strict_segment_terminator;
  public $segment_terminator;

  public $action;

  /**
   * @var array Categories
   */
  public $_categories = array(
    "format" => array(
      "encoding",
      "strict_segment_terminator",
      "segment_terminator",
    ),
    "handle" => array(
      "action"
    ),
  );

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "hprimsante_config";
    $spec->key    = "hprimsante_config_id";
    $spec->uniques["uniques"] = array("sender_id", "sender_class");
    return $spec;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    // Encoding
    $props["encoding"]                  = "enum list|UTF-8|ISO-8859-1 default|UTF-8";
    $props["strict_segment_terminator"] = "bool default|0";
    $props["segment_terminator"]        = "enum list|CR|LF|CRLF";

    //handle
    $props["action"]                    = "enum list|IPP_NDA|Patient|Sejour|Patient_Sejour default|IPP_NDA";

    return $props;
  }

  /**
   * Get config fields
   *
   * @return array
   */
  function getConfigFields() {
    return $this->_config_fields = self::$config_fields;
  }
}
