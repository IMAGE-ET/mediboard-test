<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CMbMetaObject
 * Classe abstraite de gestion des meta-objets
 */
class CMbMetaObject extends CMbObject {
  public $object_id;
  public $object_class;

  /**
   * @var CMbObject
   */
  public $_ref_object;

  /**
   * @see parent::getProps
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["object_id"]    = "ref notNull class|CMbObject meta|object_class";
    $specs["object_class"] = "str notNull class show|0";
    return $specs;
  }

  /**
   * Initialisation de l'objet relié
   *
   * @param CMbObject $object Objet relié
   *
   * @return void
   */
  function setObject(CMbObject $object) {
    $this->_ref_object  = $object;
    $this->object_id    = $object->_id;
    $this->object_class = $object->_class;
  }

  /**
   * Récupération de la liste des meta-objets
   * reliés à un objet donné
   *
   * @param CMbObject $object Objet relié
   *
   * @return CMbMetaObject[]
   */
  function loadListFor(CMbObject $object) {
    $this->setObject($object);
    return $this->loadMatchingList();
  }

  /**
   * Load target of meta object
   *
   * @param bool $cache Utilisation du cache
   *
   * @return CMbObject
   */
  function loadTargetObject($cache = true) {
    if ($this->_ref_object || !$this->object_class) {
      return $this->_ref_object;
    }
    
    if (!class_exists($this->object_class)) {
      $ex_object = CExObject::getValidObject($this->object_class);
      
      if (!$ex_object) {
        trigger_error("Unable to create instance of '$this->object_class' class", E_USER_ERROR);
        return null;
      }
      else {
        $ex_object->load($this->object_id);
        $this->_ref_object = $ex_object;
      }
    }
    else {
      $this->_ref_object = $this->loadFwdRef("object_id", $cache);
    }
    
    if (!$this->_ref_object->_id) {
      $this->_ref_object->load(null);
      $this->_ref_object->_view = "Element supprimé";
    }
    
    return $this->_ref_object;
  }

  /**
   * @see parent::loadRefsFwd
   */
  function loadRefsFwd() {  
    parent::loadRefsFwd();
    $this->loadTargetObject();
  }
}
