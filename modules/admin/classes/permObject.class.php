<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

if(!defined("PERM_DENY")) {
  define("PERM_DENY" , 0);
  define("PERM_READ" , 1);
  define("PERM_EDIT" , 2);
}

/**
 * The CPermObject class
 */
class CPermObject extends CMbObject {
	
	// Constants
  const DENY = 0;
  const READ = 1;
  const EDIT = 2;
	
	// Permissions for connected user
	static $users_perms = null;
	static $users_queries = null;
	
  // DB Table key
  var $perm_object_id = null;
  
  // DB Fields
  var $user_id      = null;
  var $object_id    = null;
  var $object_class = null;
  var $permission   = null;
  
  // References
  var $_ref_db_user   = null;
  var $_ref_db_object = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'perm_object';
    $spec->key   = 'perm_object_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["user_id"]      = "ref notNull class|CUser cascade";
    $specs["object_id"]    = "ref class|CMbObject meta|object_class cascade";
    $specs["object_class"] = "str notNull";
    $specs["permission"]   = "numchar notNull maxLength|1";
    return $specs;
  }
  
  function loadRefDBObject() {
    $this->_ref_db_object = new $this->object_class;
    $this->_ref_db_object->load($this->object_id);
  }

  function loadRefDBUser() {
    $this->_ref_db_user = new CUser;
    $this->_ref_db_user->load($this->user_id);
  }
  
  function loadRefsFwd() {
    $this->loadRefDBObject();
    $this->loadRefDBUser();
  }
  
  // Chargement des droits du user
  static function loadExactPermsObject($user_id = null){
    $perm = new CPermObject;
    $where = array(
      "user_id" => "= '$user_id'"
    );
    return $perm->loadList($where);
  }
  
  /**
   * Build the class object permission tree for given user
   * Cache the result as static member
   * @param user_id ref|CUser The concerned user, connected user if null
   * @return void
   */
	static function buildUser($user_id = null) {
    $user = CUser::get($user_id);

    // Never reload permissions for a given user
		if (isset(self::$users_perms[$user->_id])) {
			return;
		}
		
    $perm = new CPermObject;

    // Profile specific permissions
    $perms["prof"] = array();
    if ($user->profile_id) {
      $perm->user_id = $user->profile_id;
      $perms["prof"] = $perm->loadMatchingList();
    }

    // User specific permissions
		$perm->user_id = $user->_id;
		$perms["user"] = $perm->loadMatchingList();
		
		// Build final tree
		foreach($perms as $owner => $_perms) {
			foreach($_perms as $_perm) {
				self::$users_perms[$user->_id][$_perm->object_class][$_perm->object_id ? $_perm->object_id : "all"] = $_perm->permission;
			}
		}
		
    self::$users_queries = array();
	}
	
  // Those functions are statics
  static function loadUserPerms($user_id = null) {
    if (CPermModule::$system_down) {
      return true;
    }

    global $AppUI, $userPermsObjects;
    
    // Déclaration du user
    $user = new CUser();
    if($user_id !== null){
      $user->load($user_id);
    } else {
      $user->load($AppUI->user_id);
    }

    //Declaration des tableaux de droits 
    $permsObjectFinal = array();
    
    // Declaration des tableaux de droits
    $tabObjectProfil = array();
    $tabObjectSelf = array();
    
    //Chargement des droits
    $permsObjectSelf = CPermObject::loadExactPermsObject($user->user_id);
    
    // Creation du tableau de droits du user
    foreach($permsObjectSelf as $key => $value){
      $tabObjectSelf["obj_$value->object_id$value->object_class"] = $value;
    }
    
    // Creation du tableau de droits du profil
    $permsObjectProfil = CPermObject::loadExactPermsObject($user->profile_id);
    foreach($permsObjectProfil as $key => $value){
      $tabObjectProfil["obj_$value->object_id$value->object_class"] = $value;
    }
    
    // Fusion des deux tableaux de droits
    $tabObjectFinal = array_merge($tabObjectProfil, $tabObjectSelf);
    
    // Creation du tableau de fusion des droits
    foreach($tabObjectFinal as $object => $value){
      $permsObjectFinal[$value->perm_object_id] = $value;
    }

    // Tri du tableau de droit final en fonction des cle (perm_module_id)
    ksort($permsObjectFinal);

    $userPermsObjects = array();
    foreach($permsObjectFinal as $perm_obj) {
      if(!$perm_obj->object_id){
        $userPermsObjects[$perm_obj->object_class][0] = $perm_obj;
      }else{
        $userPermsObjects[$perm_obj->object_class][$perm_obj->object_id] = $perm_obj;
      }
    }
		
//		foreach($userPermsObjects as $class => $userPermByClass) {
//      foreach($userPermByClass as $userPerm) {
//	   		mbTrace($userPerm->getDBFields(), "[$userPerm->object_class][$userPerm->object_id]");
//      }
//    }
  }
  
  static function getPermObject(CMbObject $object, $permType, $defaultObject = null) {
    if (CPermModule::$system_down) {
      return true;
    }

/* Don't know what this code was for
    if (is_array(self::$users_perms)) {
    	$perms = self::$users_perms[CAppUI::$user->_id];
    	$class = $object->_class_name;
	    $id    = $object->_id;
			
    	if (!isset(self::$users_queries[$class][$id])) {
    		// query object permission and cache it
      $perm = 
        isset($perms[$class][id]    ? $perms[$class][id] :
        isset($perms[$class]["all"] ? $perms[$class]["all"] :
        CModule
    	}
			// return queried object permission
    }
*/
    global $userPermsObjects;

    $result       = PERM_DENY;
    $object_class = $object->_class_name;
    $object_id    = $object->_id;
    if(isset($userPermsObjects[$object_class][$object_id])) {
      return $userPermsObjects[$object_class][$object_id]->permission >= $permType;
    }
    if(isset($userPermsObjects[$object_class][0])) {
      return $userPermsObjects[$object_class][0]->permission >= $permType;
    }
    return $defaultObject != null ?
      $defaultObject->getPerm($permType) :
      $object->_ref_module->getPerm($permType);
  }
  
  function check() {
    $msg = null;
    $ds = $this->_spec->ds;
    
    if(!$this->perm_object_id) {
      $where = array();
      $where["user_id"]      = $ds->prepare("= %",$this->user_id);
      $where["object_class"] = $ds->prepare("= %",$this->object_class);
      if($this->object_id){
        $where["object_id"]    = $ds->prepare("= %",$this->object_id);
      }else{
        $where["object_id"]    = "IS NULL";
      }
      
      $sql = new CRequest();
      $sql->addSelect("count(perm_object_id)");
      $sql->addTable("perm_object");
      $sql->addWhere($where);
      
      $nb_result = $ds->loadResult($sql->getRequest());
      
      if($nb_result){
        $msg.= "Une permission sur cet objet existe déjà.<br />";
      }
    }
    return $msg . parent::check();
  }
}

CPermObject::loadUserPerms();
//CPermObject::buildUser();

?>