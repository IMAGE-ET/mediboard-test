<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExConcept extends CMbObject {
  var $ex_concept_id = null;
  
  var $ex_list_id = null;
  var $name = null; // != object_class, object_id, ex_ClassName_event_id, 
  var $prop = null; 
  
  var $_ref_ex_list = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_concept";
    $spec->key   = "ex_concept_id";
	  $spec->uniques["name"] = array("name", "ex_list_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_list_id"]  = "ref class|CExList";
    $props["name"]        = "str notNull";
    $props["prop"]        = "str notNull";
    return $props;
  }
	
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["class_fields"] = "CExClassField concept_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
		
    $this->_view = $this->name;
		
		if ($this->ex_list_id) {
			$list = $this->loadRefExList();
			$this->_view .= " [$list->_view]";
		}
  }
  
  function loadRefExList($cache = true){
    return $this->_ref_ex_list = $this->loadFwdRef("ex_list_id", $cache);
  }
  
  function updateTranslation(){
  	$base = $this;
		
  	if ($this->concept_id) {
  		$base = $this->loadRefConcept();
  	}
		
    $enum_trans = $base->loadRefEnumTranslations();
    foreach($enum_trans as $_enum_trans) {
      $_enum_trans->updateLocales($this);
    }
    
    $trans = $this->loadRefTranslation();
    $this->_locale       = $trans->std;
    $this->_locale_desc  = $trans->desc;
    $this->_locale_court = $trans->court;
    
    $this->_view = $this->_locale;
    
    return $trans;
  }
}
