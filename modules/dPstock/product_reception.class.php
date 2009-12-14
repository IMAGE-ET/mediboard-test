<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductReception extends CMbObject {
	// DB Table key
	var $reception_id     = null;

	// DB Fields
	var $date             = null;
	var $societe_id       = null;
  var $group_id         = null;
  var $reference        = null;

	// Object References
	//    Multiple
	var $_ref_reception_items = null;

	//    Single
	var $_ref_societe = null;
  var $_ref_group   = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "product_reception";
    $spec->key   = "reception_id";
    return $spec;
  }

	function getBackProps() {
		$backProps = parent::getBackProps();
		$backProps["reception_items"] = "CProductOrderItem order_id";
		return $backProps;
	}

	function getProps() {
		$specs = parent::getProps();
    $specs['date']       = 'dateTime seekable';
    $specs['societe_id'] = 'ref notNull class|CSociete';
    $specs['group_id']   = 'ref notNull class|CGroups';
	  $specs['reference']  = 'str notNull';
		return $specs;
	}
  
  function getUniqueNumber() {
  	$format = CAppUI::conf('dPstock CProductOrder order_number_format');
  	
    if (strpos($format, '%id') === false) {
      $format .= '%id';
    }
    
  	$format = str_replace('%id', str_pad($this->_id?$this->_id:0, 8, '0', STR_PAD_LEFT), $format);
  	return mbTransformTime(null, null, $format);
  }

	function updateFormFields() {
		parent::updateFormFields();

		$count = count($this->_ref_reception_items);
		$this->_view  = $this->_ref_societe ? "{$this->_ref_societe->_view} - " : "";
		$this->_view .= "$count article".(($count>1)?'s':'').", total = $this->_total";
	}
	
	function store () {
	  if ($msg = parent::store()) return $msg;
    
	  if (empty($this->order_number)) {
      $this->order_number = $this->getUniqueNumber();
    }
    parent::store();
	}

	function loadRefsBack(){
		$this->_ref_reception_items = $this->loadBackRefs('reception_items');
	}

	function loadRefsFwd(){
		$this->_ref_societe = $this->loadFwdRef("societe_id", true);
    $this->_ref_group = $this->loadFwdRef("group_id", true);
	}

	function getPerm($permType) {
		if(!$this->_ref_reception_items) {
			$this->loadRefsFwd();
		}

		foreach ($this->_ref_reception_items as $item) {
			if (!$item->getPerm($permType)) {
				return false;
			}
		}
		return true;
	}
}
