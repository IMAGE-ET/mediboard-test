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

/**
 * View sender source class. 
 * @abstract Encapsulate an FTP source for view sending purposes only
 */
class CViewSenderSource extends CMbObject {
  // DB Table key
  public $source_id;
  
  // DB fields
  public $name;
  public $libelle;
  public $group_id;
  public $actif;
  public $archive;
  
  // Form fields
  public $_type_echange;
  public $_ref_source_ftp;
  public $_reachable;
  
  // Distant refs
  public $_ref_senders;
      
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "view_sender_source";
    $spec->key   = "source_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["name"]     = "str notNull";
    $props["libelle"]  = "str";
    $props["group_id"] = "ref notNull class|CGroups autocomplete|text";
    $props["actif"]    = "bool notNull";
    $props["archive"]  = "bool notNull";
    
    $props["_reachable"] = "bool";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["senders_link"] = "CSourceToViewSender source_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_type_echange = $this->_class;
    $this->_view         = $this->name . ($this->libelle ? " - $this->libelle" : "");
  }

  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", 1);
  }
  
  function loadRefSourceFTP() {
    return $this->_ref_source_ftp = CExchangeSource::get("$this->_guid", "ftp", true, $this->_type_echange);
  }
  
  function loadRefSenders() {
    $senders_link = $this->loadBackRefs("senders_link");
    return $this->_ref_senders = CMbObject::massLoadFwdRef($senders_link, "sender_id");
  }
}
