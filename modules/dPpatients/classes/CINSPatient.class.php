<?php

/**
 * $Id$
 *  
 * @category Patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Description
 */
class CINSPatient extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $ins_patient_id;
  public $patient_id;
  public $ins;
  public $type;
  public $date;
  public $provider;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "ins_patient";
    $spec->key    = "ins_patient_id";
    return $spec;  
  }
  
  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    $props["patient_id"] = "ref class|CPatient notNull";
    $props["ins"]        = "str notNull";
    $props["type"]       = "enum list|A|C notNull";
    $props["date"]       = "dateTime notNull";
    $props["provider"]   = "str notNull";

    return $props;
  }
}