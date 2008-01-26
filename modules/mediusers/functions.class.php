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
  
  // Object References
  var $_ref_group = null;
  var $_ref_users = null;

  // Form fields
  var $_tel1        = null;
  var $_tel2        = null;
  var $_tel3        = null;
  var $_tel4        = null;
  var $_tel5        = null;
  var $_fax1        = null;
  var $_fax2        = null;
  var $_fax3        = null;
  var $_fax4        = null;
  var $_fax5        = null;
  
	function CFunctions() {
		$this->CMbObject("functions_mediboard", "function_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
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
     return $backRefs;
  }

  function getSpecs() {
    return array (
      "group_id" => "notNull ref class|CGroups",
      "type"     => "notNull enum list|administratif|cabinet",
      "text"     => "notNull str confidential",
      "color"    => "notNull str length|6",
      "adresse"  => "text",
      "cp"       => "numchar length|5",
      "ville"    => "str maxLength|50",
      "tel"      => "numchar length|10",
      "fax"      => "numchar length|10",
      "soustitre"=> "text"
    );
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
    
    $this->updateFormTel("tel", "_tel");
    $this->updateFormTel("fax", "_fax");
 	}
  
  function updateDBFields() {
    $this->updateDBTel("tel", "_tel");
    $this->updateDBTel("fax", "_fax");
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
    $template->addProperty("Cabinet - nom"         , $this->text      );
    $template->addProperty("Cabinet - sous-titre"  , $this->soustitre );
    $template->addProperty("Cabinet - adresse"     , $this->adresse   );
    $template->addProperty("Cabinet - cp ville"    , $this->cp ." ". $this->ville );
    $template->addProperty("Cabinet - téléphone"   , $this->tel       );
    $template->addProperty("Cabinet - fax"         , $this->fax       );
    $this->_ref_group->fillLimitedTemplate($template);
  }
}
?>