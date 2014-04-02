<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

class CFirstNameAssociativeSex extends CMbObject {
  public $first_name_id;
  public $firstname;
  public $sex;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'firstname_to_gender';
    $spec->key         = 'first_name_id';
    return $spec;
  }


  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["firstname"] = "str notNull";
    $specs["sex"]       = "enum list|f|m|u notNull default|u";
    return $specs;
  }

  /**
   * return the sex if found for firstname, else return null
   *
   * @param string $firstname the firstname to have
   *
   * @return string|null sex
   */
  static function getSexFor($firstname) {
    $object = new self();
    $object->firstname = trim($firstname);
    $object->loadMatchingObjectEsc();
    return $object->sex;
  }

}