<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Romain Ollivier
*/

/**
 * The CFunction Class
 */
class CFunctions extends CMbObject {
  // DB Table key
	var $function_id = null;

  // DB References
  var $group_id = null;

  // DB Fields
  var $type      = null;
	var $text      = null;
  var $soustitre = null;
	var $color     = null;
  var $adresse   = null;
  var $cp        = null;
  var $ville     = null;
  var $tel       = null;
  var $fax       = null;
  var $compta_partagee = null;
  
  // Object References
  var $_ref_group = null;
  var $_ref_users = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'functions_mediboard';
    $spec->key   = 'function_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["aides"] = "CAideSaisie function_id";
    $backRefs["compte_rendu"] = "CCompteRendu function_id";
    $backRefs["fiches_compta"] = "CGestionCab function_id";
    $backRefs["liste_choix"] = "CListeChoix function_id";
    $backRefs["users"] = "CMediusers function_id";
    $backRefs["paiements"] = "CModePaiement function_id";
    $backRefs["pack_examens"] = "CPackExamensLabo function_id";
    $backRefs["plages_op"] = "CPlageOp spec_id";
    $backRefs["rubriques"] = "CRubrique function_id";
    $backRefs["tarifs"] = "CTarif function_id";
    $backRefs["function_category_prescription"] = "CFunctionCategoryPrescription function_id";
    return $backRefs;
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["group_id"] = "notNull ref class|CGroups";
    $specs["type"]     = "notNull enum list|administratif|cabinet";
    $specs["text"]     = "notNull str confidential";
    $specs["color"]    = "notNull str length|6";
    $specs["adresse"]  = "text";
    $specs["cp"]       = "numchar length|5";
    $specs["ville"]    = "str maxLength|50";
    $specs["tel"]      = "numchar length|10 mask|99S99S99S99S99";
    $specs["fax"]      = "numchar length|10 mask|99S99S99S99S99";
    $specs["soustitre"]= "text";
    $specs["compta_partagee"]= "notNull bool";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "text" => "like"
    );
  }
  
  function updateFormFields() {
		parent::updateFormFields();

    $this->_view = $this->text;
    if(strlen($this->text) > 25)
      $this->_shortview = substr($this->text, 0, 23)."...";
    else
      $this->_shortview = $this->text;
 	}
  
  // Forward references
  function loadRefsFwd() {
    $this->_ref_group = new CGroups();
    $this->_ref_group->load($this->group_id);
  }
  
  // Backward references
  function loadRefsBack() {
    $this->loadRefsUsers();
  }
  
  function loadRefsUsers($type = null) {
    if(!$type) {
      $where = array();
      $where["function_id"] = "= '$this->function_id'";
      $ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";
      $order = "`users`.`user_last_name`, `users`.`user_first_name`";
      $this->_ref_users = new CMediusers;
      $this->_ref_users = $this->_ref_users->loadList($where, $order, null, null, $ljoin);
    } else {
      $this->_ref_users = new CMediusers;
      $this->_ref_users = $this->_ref_users->loadListFromType($type, PERM_READ, $this->function_id);
    }
  }
  
  // @todo : ameliorer le choix des spécialités
  // (loadfunction($groupe, $permtype) par exemple)
  function loadSpecialites($perm_type = null) {
    global $g;
    $where = array();
    $where["type"] = "= 'cabinet'";
    $where["group_id"] = "= '$g'";
    $order = "text";
    $specs = $this->loadListWithPerms($perm_type, $where, $order);
    return $specs;
  }
  
  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_group->fillTemplate($template);
    $template->addProperty("Cabinet - nom"         , $this->text      );
    $template->addProperty("Cabinet - sous-titre"  , $this->soustitre );
    $template->addProperty("Cabinet - adresse"     , $this->adresse   );
    $template->addProperty("Cabinet - cp ville"    , $this->cp ." ". $this->ville );
    $template->addProperty("Cabinet - téléphone"   , $this->tel       );
    $template->addProperty("Cabinet - fax"         , $this->fax       );
  }
}
?>