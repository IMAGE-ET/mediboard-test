<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDailyCheckList extends CMbObject { // not a MetaObject, as there can be multiple objects for different dates
  var $daily_check_list_id  = null;

  // DB Fields
  var $date         = null;
  var $object_class = null;
  var $object_id    = null;
  var $type         = null;
  var $comments     = null;
  var $validator_id = null;
	
  // Refs
  var $_ref_validator = null;
  var $_ref_object    = null;
	
  // Form fields
  var $_ref_item_types = null;
  var $_items          = null;
  var $_validator_password = null;
  var $_readonly       = null;
  var $_date_min       = null;
  var $_date_max       = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_list';
    $spec->key   = 'daily_check_list_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['date']         = 'date notNull';
    $specs['object_class'] = 'enum list|CSalle|CBlocOperatoire|COperation notNull default|CSalle';
    $specs['object_id']    = 'ref class|CMbObject meta|object_class notNull autocomplete';
    $specs['type']         = 'enum list|preanesth|preop|postop';
    $specs['validator_id'] = 'ref class|CMediusers';
    $specs['comments']     = 'text';
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
    $this->_view = "$this->_ref_object le $this->date ($this->_ref_validator)";
  }
  
  function isReadonly() {
    $this->completeField("validator_id");
    $this->completeField("date");
    return $this->_readonly = ($this->_id && $this->validator_id && $this->date);
  }
  
  function loadRefsFwd() {
    if ($this->object_class)
      $this->_ref_object = $this->loadFwdRef("object_id", true);

    $this->_ref_validator = $this->loadFwdRef("validator_id", true); 
  }
	
	function store() {
		// Est-ce un nouvel objet
		$is_new = !$this->_id;
		
		if ($msg = parent::store()) return $msg;

		if ($is_new || $this->validator_id) {
			// Sauvegarde des items cochés
	    $items = $this->_items ? $this->_items : array();
      
			$this->loadItemTypes();
		  foreach($this->_ref_item_types as $type) {
				$check_item = new CDailyCheckItem;
				$check_item->list_id = $this->_id;
				$check_item->item_type_id = $type->_id;
				$check_item->loadMatchingObject();
				$check_item->checked = (isset($items[$type->_id]) ? $items[$type->_id] : "");
				if ($msg = $check_item->store()) return $msg;
			}
			
			// Vérification du mot de passe
      if (!$this->_validator_password) {
        return 'Veuillez taper votre mot de passe';
      }
      $this->loadRefsFwd();
      $user = new CUser;
      $user->user_username = $this->_ref_validator->_user_username;
      $user->_user_password = $this->_validator_password;
      
      if (!$user->loadMatchingObject()) {
        $this->validator_id = "";
        $msg = 'Le mot de passe entré n\'est pas correct';
      }
			
			return $msg . parent::store();
		}
	}
	
	static function getList($object, $date = null, $type = null){
    $list = new self;
    $list->object_class = $object->_class_name;
    $list->object_id = $object->_id;
    $list->date = $date;
    $list->type = $type;
    $list->loadRefsFwd();
    $list->loadMatchingObject();
    $list->isReadonly();
		return $list;
	}
	
	function loadItemTypes() {
    $where = array(
      'active' => "= '1'",
      'daily_check_item_category.target_class' => "= '$this->object_class'"
    );
    if ($this->type) {
      $where['daily_check_item_category.type'] = "= '$this->type'";
    }
    $ljoin = array(
      'daily_check_item_category' => 'daily_check_item_category.daily_check_item_category_id = daily_check_item_type.category_id'
    );
    
    $orderby = 'daily_check_item_category.title, ';
    // Si liste des points de la HAS
    if ($this->object_class == "COperation") {
      $orderby .= "daily_check_item_type_id";
    }
    else {
      $orderby .= "title";
    }
    
		$this->_ref_item_types = CDailyCheckItemType::loadGroupList($where, $orderby, null, null, $ljoin);
		foreach($this->_ref_item_types as $type) {
			$type->loadRefsFwd();
		}
		$this->loadBackRefs('items');
		if ($this->_back['items']) {
			foreach($this->_back['items'] as $item) {
			  if (isset($this->_ref_item_types[$item->item_type_id])) {
	        $this->_ref_item_types[$item->item_type_id]->_checked = $item->checked;
          $this->_ref_item_types[$item->item_type_id]->_answer = $item->getAnswer();
        }
	    }
		}
	}
}
?>