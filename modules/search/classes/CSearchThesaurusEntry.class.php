<?php

/**
 * $Id$
 *
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CSearchThesaurusEntry extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $search_thesaurus_entry_id;

  // DB fields
  public $entry;
  public $types;
  public $titre;
  public $contextes;
  public $agregation;
  public $group_id;
  public $function_id;
  public $user_id;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = "search_thesaurus_entry";
    $spec->key   = "search_thesaurus_entry_id";

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
    $props["entry"]         = "str notNull maxLength|255 seekable";
    $props["types"]         = "str maxLength|255";
    $props["titre"]         = "str maxLength|255 seekable";
    $props["contextes"]     = "enum list|".implode("|", CSearchLog::$names_mapping);
    $props["group_id"]      = "ref class|CGroups";
    $props["function_id"]   = "ref class|CFunctions";
    $props["user_id"]       = "ref class|CMediusers notNull";
    $props["agregation"]    = "enum list|0|1 default|0";

    return $props;
  }
}
