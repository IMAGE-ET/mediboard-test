<?php

/**
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CSearchIndexing extends CStoredObject {
  /**
   * @var integer Primary key
   */
  public $search_indexing_id;

  // DB Fields
  public $type;
  public $object_class;
  public $object_id;
  public $date;
  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = "search_indexing";
    $spec->key   = "search_indexing_id";
    $spec->loggable = false;
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
    $props["object_class"] = "str notNull maxLength|50";
    $props["object_id"] = "ref class meta|object_class notNull unlink";
    $props["type"] = "enum list|create|store|delete|merge default|create";
    $props["date"] = "dateTime notNull";

    return $props;
  }

  /**
   * @see parent::store()
   */
  function store() {
    return parent::store();
  }
}
