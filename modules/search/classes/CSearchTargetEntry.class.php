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
class CSearchTargetEntry extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $search_thesaurus_entry_target_id;

  public $search_thesaurus_entry_id;
  public $object_class;
  public $object_id;

  public $_ref_target;
  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = "search_thesaurus_entry_target";
    $spec->key   = "search_thesaurus_entry_target_id";
    $spec->uniques["code"]   = array("search_thesaurus_entry_id", "object_class", "object_id");

    return $spec;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["search_thesaurus_entry_id"] = "ref class|CSearchThesaurusEntry cascade";
    $props["object_class"] = "str maxLength|50";
    $props["object_id"] = "str maxLength|50";

    return $props;
  }

  /**
   * Method to load the target object of thesaurus target
   *
   * @return CActeNGAP|CCodeCCAM|CCodeCIM10
   */
  function loadRefTarget() {
    if ($this->object_class && $this->object_id) {
      switch ($this->object_class) {
        case "CCodeCIM10":
          $object       = new CCodeCIM10();
          $object->code = $this->object_id;
          $object->loadLite();
          $this->_ref_target = $object;
          break;
        case "CCodeCCAM":
          $object = new CCodeCCAM($this->object_id);
          $object->load();
          $this->_ref_target = $object;
          break;
        case "CActeNGAP":
          $object       = new CActeNGAP();
          $object->code = $this->object_id;
          $object->loadMatchingObject();
          $this->_ref_target = $object;
          break;
        case "CMedicamentClasseATC" :
          $object = new CMedicamentClasseATC();
          $niveau = $object->getNiveau($this->object_id);
          $object->loadClasseATC($niveau, $this->object_id);
          $this->_ref_target = $object;
          break;
        default:
          // nothing to do
          break;
      }
    }
    return $this->_ref_target;
  }
}
