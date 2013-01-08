<?php /* $Id: CIdSante400.class.php 13724 2011-11-09 15:10:29Z lryo $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: 13724 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CIncrementer extends CMbObject {
  // DB Table key
  var $incrementer_id = null;
  
  // DB fields
  var $last_update  = null;
  var $value        = null;
  var $pattern      = null;
  var $range_min    = null;
  var $range_max    = null;
  
  // Form fields
  var $_object_class = null;
  
  /**
   * @var CDomain
   */
  var $_ref_domain     = null;
  
  /**
   * @return CGroupDomain
   */
  var $_ref_group_domain = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    
    $spec->table = 'incrementer';
    $spec->key   = 'incrementer_id';
    $spec->loggable = false;
    
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["value"]        = "str notNull default|1";
    $props["pattern"]      = "str notNull";
    $props["range_min"]    = "num min|0";
    $props["range_max"]    = "num moreThan|range_min";
    $props["last_update"]  = "dateTime notNull";
    
    $props["_object_class"] = "enum notNull list|CPatient|CSejour";

    return $props;
  } 
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    $backProps["domains"] = "CDomain incrementer_id";
    
    return $backProps;
  }
  
  /**
   * @return CDomain
   */
  function loadRefDomain() {
    if ($this->_ref_domain) {
      return $this->_ref_domain;
    }
    
    return $this->_ref_domain = $this->loadUniqueBackRef("domains");
  }
  
  /**
   * @return CGroupDomain
   */
  function loadMasterDomain() {
    if ($this->_ref_group_domain) {
      return $this->_ref_group_domain;
    }
    
    $this->loadRefDomain();
    
    $group_domain            = new CGroupDomain();
    $group_domain->domain_id = $this->_ref_domain->_id;
    $group_domain->master    = 1;
    $group_domain->loadMatchingObject();
    
    $this->_object_class = $group_domain->object_class;
    
    return $this->_ref_group_domain = $group_domain;
  }
  
  function loadView() {
    if (!$this->_id) {
      return;
    }
    
    parent::loadView();
    
    $this->loadMasterDomain();

    $object = new $this->_object_class;
    $this->_view = self::formatValue($object, $this->pattern, $this->value);
  }
  
  static function generateIdex(CMbObject $object, $tag, $group_id) {
    $group_domain               = new CGroupDomain();
    $group_domain->object_class = $object->_class;
    $group_domain->group_id     = $group_id;
    $group_domain->master       = 1;
    $group_domain->loadMatchingObject();
    if (!$group_domain->_id) {
      return;
    }
    
    $mutex = new CMbSemaphore("incrementer-$object->_class");
    $mutex->acquire();
    
    $incrementer = $group_domain->loadRefDomain()->loadRefIncrementer();
    // Chargement du dernier 'increment' s'il existe sinon on déclenche une erreur
    if (!$incrementer->_id) {
      $mutex->release();
      return;
    }

    $format_value = self::formatValue($object, $incrementer->pattern, $incrementer->value);
    
    // Création de l'identifiant externe
    $id400               = new CIdSante400();
    $id400->object_id    = $object->_id;
    $id400->object_class = $object->_class;
    $id400->tag          = $tag;
    $id400->id400        = $format_value;
    $id400->last_update  = mbDateTime();
    $id400->store();
    
    // Incrementation de l'idex
    $incrementer->value++;
    $incrementer->last_update = mbDateTime();
    $incrementer->store();

    $mutex->release();

    return $id400;
  }
  
  static function getVars(CMbObject $object) {
    $vars = $object->getIncrementVars();
    $default_vars = array(
      "YYYY" => mbTransformTime(null, null, "%Y"),
      "YY"   => mbTransformTime(null, null, "%y"),
    );
    $vars = array_merge($vars, $default_vars);
    
    return $vars;
  }
  
  static function formatValue(CMbObject $object, $pattern, $value) {
    $vars = self::getVars($object);
    
    foreach ($vars as $_var => $_value) {
      $pattern = str_replace("[$_var]", $_value, $pattern); 
    } 
    
    return sprintf($pattern, $value);
  }
}