<?php

/**
 * $Id$
 *
 * @category Etablissement
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CLegalStatus extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $status_code;
  public $legal_status_niv_3;
  public $name;
  public $short_name;


  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->dsn   = 'sae';
    $spec->table = "legal_status";
    $spec->key   = "status_code";

    return $spec;
  }

  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["legal_entity"] = "CLegalEntity legal_status_code";

    return $backProps;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    $props["legal_status_niv_3"]  = "num notNull maxLength|5";
    $props["name"]                = "str notNull";
    $props["short_name"]          = "str notNull";

    return $props;
  }

}
