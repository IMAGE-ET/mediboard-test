<?php

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage dicom
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

/**
 * Description
 */
class CDicomConfig extends CExchangeDataFormatConfig {
  /**
   * @var array Config fields
   */
  public static $config_fields = array(
    'send_0032_1032',
    'value_0008_0060'
  );

  /**
   * @var array Categories
   */
  public $_categories = array(
    'fields' => array(
      'send_0032_1032',
    ),
    'values' => array(
      'value_0008_0060'
    )
  );

  /**
   * @var integer Primary key
   */
  public $dicom_config_id;

  public $send_0032_1032;
  public $value_0008_0060;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "dicom_configs";
    $spec->key    = "dicom_config_id";
    $spec->uniques['uniques'] = array('sender_id', 'sender_class');

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

    $props['sender_class']    = 'enum list|CDicomSender show|0 default|CDicomSender';
    $props['send_0032_1032']  = 'bool default|0';
    $props['value_0008_0060'] = 'str';

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
