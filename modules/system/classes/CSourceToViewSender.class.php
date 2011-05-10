<?php /* $Id: message.class.php 8208 2010-03-04 19:14:03Z lryo $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSourceToViewSender extends CMbObject {
  // DB Table key
  var $source_to_view_sender_id = null; 
  
  // DB fields
  var $source_id = null;
  var $sender_id = null;
  
  // Form fields
  var $_ref_source = null;
  var $_ref_sender = null;
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "source_to_view_sender";
    $spec->key   = "source_to_view_sender_id";
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["sender_id"] = "ref class|CViewSender notNull autocomplete|name";
    $props["source_id"] = "ref class|CViewSenderSource notNull autocomplete|name";
    return $props;
  }
  
  function loadRefSender() {
    return $this->_ref_sender = $this->loadFwdRef("sender_id", 1);
  }
  
  function loadRefSource() {
    return $this->_ref_source = $this->loadFwdRef("source_id", 1);
  }
}

?>