<?php

/**
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CSearchItem extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $rss_search_item_id;
  public $rss_id;
  public $search_id;
  public $search_class;
  public $rmq;
  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = "rss_search_items";
    $spec->key   = "rss_search_item_id";

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
    $props['rss_id'] = "ref notNull class|CRSS";
    $props['search_id'] = "num";
    $props['search_class'] = "str maxLength|40";
    $props['rmq'] = "text";
    return $props;
  }
}
