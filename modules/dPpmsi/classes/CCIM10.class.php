<?php

/**
 * $Id$
 *
 * @category pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CCIM10 extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $code;

  public $type;
  public $short_name;
  public $complete_name;
  public $exist;


  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = "CIM10";
    $spec->key   = "code";
    $spec->dsn   = "cim10";

    return $spec;
  }


  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props[$this->getSpec()->key] .= " seekable";
    $props["type"]          = "enum list|0|1|2|3|4";
    $props["short_name"]    = "str seekable";
    $props["complete_name"] = "str seekable";

    return $props;
  }

  /**
   * @param null $id
   */
  function load($id = null) {
    parent::load($id);
    if ($this->_id) {
      $this->exist = true;
    }
    return $this;
  }

  /**
   * @param $code
   *
   * @return $this|CCIM10|CMbObject
   */
  static function get($code) {
    $cim = new CCIM10();
    return $cim->load($code);
  }
}
