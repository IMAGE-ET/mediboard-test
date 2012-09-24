<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$ 
 */

/**
 * Remplacement d'un mot-clé par une plus longue chaîne de caractères
 * S'associe sur toute propriété d'une classe dont la spec contient helped
 */
class CAideSaisie extends CMbObject {
  // DB Table key
  var $aide_id = null;

  // DB References
  var $user_id            = null;
  var $function_id        = null;
  var $group_id           = null;

  // DB fields
  var $class              = null;
  var $field              = null;
  var $name               = null;
  var $text               = null;
  var $depend_value_1     = null;
  var $depend_value_2     = null;
  
  // Form Fields
  var $_depend_field_1    = null;
  var $_depend_field_2    = null;
  var $_owner             = null;
  var $_vw_depend_field_1 = null;
  var $_vw_depend_field_2 = null;
  var $_is_ref_dp_1       = null;
  var $_is_ref_dp_2       = null;
  var $_class_dp_1        = null;
  var $_class_dp_2        = null;
  var $_applied           = null;
  
  // Referenced objects
  var $_ref_user          = null;
  var $_ref_function      = null;
  var $_ref_group         = null;
  var $_ref_owner         = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'aide_saisie';
    $spec->key   = 'aide_id';
    $spec->xor["owner"] = array("user_id", "function_id", "group_id");
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["user_id"]      = "ref class|CMediusers";
    $specs["function_id"]  = "ref class|CFunctions";
    $specs["group_id"]     = "ref class|CGroups";
    
    $specs["class"]        = "str notNull";
    $specs["field"]        = "str notNull";
    $specs["name"]         = "str notNull seekable";
    $specs["text"]         = "text notNull seekable";
    $specs["depend_value_1"] = "str";
    $specs["depend_value_2"] = "str";
    
    $specs["_depend_field_1"] = "str";
    $specs["_depend_field_2"] = "str";
    $specs["_vw_depend_field_1"] = "str";
    $specs["_vw_depend_field_2"] = "str";
    $specs["_owner"]          = "enum list|user|func|etab";
    
    return $specs;
  }
  
  /**
   * Vérifie l'unicité d'une aide à la saisie
   * 
   * @return string
   */
  function check() {
    $msg = "";
    
    $ds = $this->_spec->ds;
    
    $where = array();
    if ($this->user_id) {
      $where["user_id"] = $ds->prepare("= %", $this->user_id);
    }
    else if ($this->function_id) {
      $where["function_id"] = $ds->prepare("= %", $this->function_id);
    }
    else {
      $where["group_id"] = $ds->prepare("= %", $this->group_id);
    }
      
    $where["class"]          = $ds->prepare("= %",  $this->class);
    $where["field"]          = $ds->prepare("= %",  $this->field);
    $where["depend_value_1"] = $ds->prepare("= %",  $this->depend_value_1);
    $where["depend_value_2"] = $ds->prepare("= %",  $this->depend_value_2);
    $where["text"]           = $ds->prepare("= %",  $this->text);
    $where["aide_id"]        = $ds->prepare("!= %", $this->aide_id);
    
    $sql = new CRequest();
    $sql->addSelect("count(aide_id)");
    $sql->addTable("aide_saisie");
    $sql->addWhere($where);
    
    $nb_result = $ds->loadResult($sql->getRequest());
    
    if ($nb_result) {
      $msg .= "Cette aide existe déjà<br />";
    }
    
    return $msg . parent::check();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->name;
    
    // Owner
    if ($this->user_id ) {
      $this->_owner = "user";
    }
    if ($this->function_id) {
      $this->_owner = "func";
    }
    if ($this->group_id) {
      $this->_owner = "etab"; 
    }
    
    // Depend fields
    if ($this->class) {
      $object = new $this->class;
      $helped = $object->_specs[$this->field]->helped;
      $this->_depend_field_1 = isset($helped[0]) ? $helped[0] : null; 
      $this->_depend_field_2 = isset($helped[1]) ? $helped[1] : null;
      
      switch($this->class) {
        case "CTransmissionMedicale":
          $this->_class_dp_2 = "CCategoryPrescription";
          break;
      }
    }
  }
  
  /**
   * Charge l'utilisateur associé à l'aide
   * 
   * @param boolean $cached Charge l'utilisateur depuis le cache
   * 
   * @return CMediusers
   */
  function loadRefUser($cached = true){
    return $this->_ref_user = $this->loadFwdRef("user_id", $cached);
  }
  
  /**
   * Charge la fonction associée à l'aide
   * 
   * @param boolean $cached Charge la fonction depuis le cache
   * 
   * @return CFunctions
   */
  function loadRefFunction($cached = true){
    return $this->_ref_function = $this->loadFwdRef("function_id", $cached);
  }
  
  /**
   * Charge l'établissement associé à l'aide
   * 
   * @param boolean $cached Charge l'établissement depuis le cache
   * 
   * @return CGroups
   */
  function loadRefGroup($cached = true){
    return $this->_ref_group = $this->loadFwdRef("group_id", $cached);
  }
  
  function loadRefsFwd() {
    $this->loadRefUser(true);
    $this->loadRefFunction(true);
    $this->loadRefGroup(true);
    $this->searchRefsObject();
  }
  
  /**
   * Charge le propriétaire de l'aide
   * 
   * @return CMediusers || CFunctions || CGroups
   */
  function loadRefOwner(){
    $this->loadRefsFwd();
    if ($this->user_id) {
      return $this->_ref_owner = $this->_ref_user;
    }
    if ($this->function_id) {
      return $this->_ref_owner = $this->_ref_function;
    }
    if ($this->group_id) {
      return $this->_ref_owner = $this->_ref_group;
    }
  }
  
  /**
   * Permission generic check
   * 
   * @param Const $permType Type of permission : PERM_READ|PERM_EDIT|PERM_DENY
   * 
   * @return boolean
   */
  function getPerm($permType) {
    if (!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return $this->_ref_user->getPerm($permType);
  }
  
  /**
   * Traduit les depend fields
   * 
   * @param CMbObject $object L'objet sur lequel sont appliquées les valeurs de dépendances 
   * 
   * @return void
   */
  function loadViewDependValues($object) {
    $this->_vw_depend_field_1 =
      CAppUI::isTranslated("$object->_class.$this->_depend_field_1.$this->depend_value_1") ?
        CAppUI::tr("$object->_class.$this->_depend_field_1.$this->depend_value_1") : 
        $this->_vw_depend_field_1 = $this->depend_value_1;
    $this->_vw_depend_field_2 =
      CAppUI::isTranslated("$object->_class.$this->_depend_field_2.$this->depend_value_2") ?
        CAppUI::tr("$object->_class.$this->_depend_field_2.$this->depend_value_2") : 
        $this->_vw_depend_field_2 = $this->depend_value_2;
  }
  
  /**
   * Charge les objets référencés par l'aide
   * 
   * @return void
   */
  function searchRefsObject() {
    $this->_is_ref_dp_1 = false;
    $this->_is_ref_dp_2 = false;
    
    $object = new $this->class;
    $field = $this->field;
    $helped = array();
    if ($object->_specs[$field]->helped && !is_bool($object->_specs[$field]->helped)) {
      if (!is_array($object->_specs[$field]->helped)) {
        $helped = array($object->_specs[$field]->helped);
      }
      else {
        $helped = $object->_specs[$field]->helped;
      }
    }
    foreach ($helped as $i => $depend_field) {
      $spec = $object->_specs[$depend_field];
      if ($spec instanceof CRefSpec) {
        $key = "_is_ref_dp_".($i+1);
        $this->$key = true;
      }
    }
  }
}

?>