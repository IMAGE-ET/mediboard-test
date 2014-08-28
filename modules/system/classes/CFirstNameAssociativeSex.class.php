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
  public $language;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'firstname_to_gender';
    $spec->key         = 'first_name_id';
    $spec->loggable = false;
    return $spec;
  }


  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["firstname"] = "str notNull";
    $specs["sex"]       = "enum list|f|m|u notNull default|u";
    $specs["language"]  = "str";
    return $specs;
  }

  /**
   * return the sex if found for firstname, else return null
   *
   * @param string $firstname the firstname to have
   *
   * @return string|null sex (u = undefined, f = female, m = male, null = not in base)
   */
  static function getSexFor($firstname) {
    $prenom_exploded = preg_split('/[-_ ]+/', $firstname);   // get the first firstname of composed one
    $first_first_name = addslashes(trim(reset($prenom_exploded)));

    $object = new self();
    $object->firstname = $first_first_name;
    $nb_objects = $object->countMatchingList();
    if ($nb_objects > 1) {
      $object->language = "french";
      $object->loadMatchingObject();
    }
    if (!$object->_id || $object->sex == "u") {
      $object = new self();
      $object->firstname = $first_first_name;
      $object->loadMatchingObject();
    }

    return $object->sex ? $object->sex : "u";
  }

  static function countData() {
    $fs = new self();
    return $fs->countList();
  }
}