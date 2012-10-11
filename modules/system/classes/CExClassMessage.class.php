<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassMessage extends CMbObject {
  var $ex_class_message_id = null;
  
  var $ex_group_id = null;
  var $type    = null;
  
  var $title   = null;
  var $text    = null;
  
  var $coord_title_x = null;
  var $coord_title_y = null;
  var $coord_text_x = null;
  var $coord_text_y = null;
  
  var $_ref_ex_group = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_message";
    $spec->key   = "ex_class_message_id";
    $spec->uniques["coord"] = array("ex_group_id", "coord_title_x", "coord_title_y", "coord_text_x", "coord_text_y");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_group_id"] = "ref notNull class|CExClassFieldGroup cascade";
    $props["type"]        = "enum list|title|info|warning|error";
    
    $props["title"]       = "str";
    $props["text"]        = "text notNull";
    
    $props["coord_title_x"] = "num min|0 max|100";
    $props["coord_title_y"] = "num min|0 max|100";
    $props["coord_text_x"] = "num min|0 max|100";
    $props["coord_text_y"] = "num min|0 max|100";
    return $props;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = ($this->title ? $this->title : CMbString::truncate($this->text, 30));
  }
  
  function loadRefExGroup($cache = true){
    return $this->_ref_ex_group = $this->loadFwdRef("ex_group_id", $cache);
  }
}
