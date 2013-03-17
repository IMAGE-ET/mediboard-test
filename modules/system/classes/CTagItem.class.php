<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CTagItem extends CMbMetaObject {
  public $tag_item_id;
  
  public $tag_id;
  
  /**
   * @var CTag
   */
  public $_ref_tag;

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
    if ($msg = parent::check()) {
      return $msg;
    }
    
    $this->completeField("tag_id", "object_class");
    
    if ($this->loadRefTag()->object_class !== $this->object_class) {
      return "L'objet et le tag doivent appartenir à la même classe";
    }
  }
}
