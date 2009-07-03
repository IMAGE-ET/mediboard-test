<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDailyCheckList extends CMbObject {
  var $daily_check_list_id  = null;

  // DB Fields
  var $date         = null;
	var $room_id      = null;
  var $validator_id = null;
	
	// Refs
  var $_ref_validator = null;
	var $_ref_room      = null;
	
	// Form fields
	var $_ref_item_types = null;
	var $_items          = null;
	var $_validator_password = null;
	var $_date_min = null;
  var $_date_max = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_list';
    $spec->key   = 'daily_check_list_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['date']         = 'date notNull';
    $specs['validator_id'] = 'ref class|CMediusers';
    $specs['room_id']      = 'ref notNull class|CSalle autocomplete|nom';
		$specs['_validator_passord'] = 'password notNull';
    $specs['_date_min'] = 'date';
    $specs['_date_max'] = 'date';
    return $specs;
  }
	
	function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['items'] = 'CDailyCheckItem list_id';
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = "$this->_ref_room le $this->date ($this->_ref_validator)";
  }
  
  function loadRefsFwd() {
    $this->_ref_room = new CSalle();
    $this->_ref_room = $this->_ref_room->getCached($this->room_id); 
		
    $this->_ref_validator = new CMediusers();
    $this->_ref_validator = $this->_ref_validator->getCached($this->validator_id); 
  }
	
	function store() {
		// Suppression du validateur, donc impossible de le changer
		$validator_id = $this->validator_id;
		$this->validator_id = null;
		
		// Est-ce un nouvel objet
		$is_new = !$this->_id;
		
		if ($msg = parent::store()) return $msg;

		if ($is_new || $validator_id) {
			// Sauvegarde des items cochés
	    $items = $this->_items ? $this->_items : array();
			$this->loadItemTypes();
		  foreach($this->_ref_item_types as $type) {
				$check_item = new CDailyCheckItem;
				$check_item->list_id = $this->_id;
				$check_item->item_type_id = $type->_id;
				$check_item->loadMatchingObject();
				$check_item->checked = in_array($type->_id, $items) ? 1 : 0;
				if ($msg = $check_item->store()) return $msg;
			}
			
			// Vérification du mot de passe
      if (!$this->_validator_password) {
        return 'Veuillez taper votre mot de passe';
      }
			$this->validator_id = $validator_id;
      $this->loadRefsFwd();
      $user = new CUser;
      $user->user_username = $this->_ref_validator->_user_username;
      $user->_user_password = $this->_validator_password;
      if (!$user->loadMatchingObject()) {
        return 'Le mot de passe entré n\'est pas correct';
      }
			
			return parent::store();
		}
	}
	
	static function getTodaysList($room_id){
		$todays_list = new self;
		$todays_list->date = mbDate();
    $todays_list->room_id = $room_id;
    $todays_list->loadMatchingObject();
		return $todays_list;
	}
	
	function loadItemTypes() {
		$where = array(
		  'active' => "= '1'"
		);
        $ljoin = array(
          'daily_check_item_category' => 'daily_check_item_category.daily_check_item_category_id = daily_check_item_type.category_id'
        );
    
		$this->_ref_item_types = CDailyCheckItemType::loadGroupList($where, 'daily_check_item_category.title, title', null, null, $ljoin);
		foreach($this->_ref_item_types as &$type) {
			$type->loadRefsFwd();
		}
		$this->loadBackRefs('items');
		if ($this->_back['items']) {
			foreach($this->_back['items'] as &$item) {
	      $this->_ref_item_types[$item->item_type_id]->_checked = $item->checked;
	    }
		}
	}
}
?>