<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CTagItem extends CMbMetaObject {
  var $tag_item_id = null;
  
  var $tag_id = null;
  
  /**
   * @var CTag
   */
  var $_ref_tag = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "tag_item";
    $spec->key   = "tag_item_id";
    $spec->uniques["object"] = array("object_class", "object_id", "tag_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["tag_id"] = "ref notNull class|CTag";
    $props["object_id"] .= " cascade seekable";
    return $props;
  }
  
  /**
   * @param bool $cache [optional]
   *
   * @return CTag
   */
  function loadRefTag($cache = true) {
    return $this->_ref_tag = $this->loadFwdRef("tag_id", $cache);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->loadRefTag()->_view;
  }
  
  function check(){
    if ($msg = parent::check()){
      return $msg;
    }
    
    $this->completeField("tag_id", "object_class");
    
    if ($this->loadRefTag()->object_class !== $this->object_class) {
      return "L'objet et le tag doivent appartenir à la même classe";
    }
  }
}
