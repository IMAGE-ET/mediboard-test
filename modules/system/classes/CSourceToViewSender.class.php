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
  var $last_datetime = null;
  var $last_duration = null;
  var $last_size     = null;
  var $last_status   = null;
  var $last_count    = null;
    
  // Form fields
  var $_last_age = null;
  
  // Object references
  var $_ref_source = null;
  var $_ref_sender = null;
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "source_to_view_sender";
    $spec->key   = "source_to_view_sender_id";
    $spec->loggable = false;
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["sender_id"] = "ref class|CViewSender notNull autocomplete|name";
    $props["source_id"] = "ref class|CViewSenderSource notNull autocomplete|name";
    $props["last_datetime"] = "dateTime";
    $props["last_duration"] = "float";
    $props["last_size"    ] = "num min|0";
    $props["last_status"  ] = "enum list|triggered|uploaded|checked";
    $props["last_count"   ] = "num min|0";

    $props["_last_age"   ] = "num";
    return $props;
  }
  
  function loadRefSender() {
    $sender = $this->loadFwdRef("sender_id", 1);
    $this->_last_age = mbMinutesRelative($this->last_datetime, mbDateTime());
  	return $this->_ref_sender = $sender;
  }
  
  function loadRefSource() {
    return $this->_ref_source = $this->loadFwdRef("source_id", 1);
  }
}

?>