<?php

/**
 * $Id$
 *
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org
 */

/**
 * Classe utilisée pour le marquage des éléments pertinents du Pmsi
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
  public $user_id;

  /** @var  CMediusers $_ref_mediuser */
  public $_ref_mediuser;

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
    $props['user_id'] = "ref class|CMediusers";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->rmq;
  }

  /**
   * Load method for ref mediuser
   *
   * @return CMediusers|null
   */
  function loadRefMediuser() {
    return $this->_ref_mediuser = $this->loadFwdRef("user_id");
  }

  function loadView() {
    parent::loadView();
    $this->loadRefMediuser()->loadRefFunction();
  }
}
