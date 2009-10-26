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
  
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["users"]                          = "CMediusers function_id";
	  $backProps["secondary_functions"]            = "CSecondaryFunction function_id";
	  $backProps["aides"]                          = "CAideSaisie function_id";
	  $backProps["catalogues_labo"]                = "CCatalogueLabo function_id";
	  $backProps["compte_rendu"]                   = "CCompteRendu function_id";
	  $backProps["consultation_cats"]              = "CConsultationCategorie function_id";
	  $backProps["employes"]                       = "CEmployeCab function_id";
	  $backProps["executants_prescription"]        = "CFunctionCategoryPrescription function_id";
	  $backProps["fiches_compta"]                  = "CGestionCab function_id";
	  $backProps["services_urgence_pour"]          = "CGroups service_urgences_id";
	  $backProps["liste_choix"]                    = "CListeChoix function_id";
	  $backProps["paiements"]                      = "CModePaiement function_id";
	  $backProps["pack_examens"]                   = "CPackExamensLabo function_id";
	  $backProps["plages_op"]                      = "CPlageOp spec_id";
	  $backProps["plages_op_repl"]                 = "CPlageOp spec_repl_id";
	  $backProps["prescriptions"]                  = "CPrescription function_id";
	  $backProps["packs_prescription_protocole"]   = "CPrescriptionProtocolePack function_id";
	  $backProps["rubriques"]                      = "CRubrique function_id";
	  $backProps["tarifs"]                         = "CTarif function_id";
	  return $backProps;
	}
	
  function getProps() {
  	$specs = parent::getProps();
    $phone_number_format = str_replace(' ', 'S', CAppUI::conf("system phone_number_format"));
    
    $specs["group_id"]        = "ref notNull class|CGroups";
    $specs["type"]            = "enum notNull list|administratif|cabinet";
    $specs["text"]            = "str notNull confidential seekable";
    $specs["color"]           = "str notNull length|6";
    $specs["adresse"]         = "text";
    $specs["cp"]              = "numchar length|5";
    $specs["ville"]           = "str maxLength|50";
    $specs["tel"]             = "numchar length|10 mask|$phone_number_format";
    $specs["fax"]             = "numchar length|10 mask|$phone_number_format";
    $specs["soustitre"]       = "text";
    $specs["compta_partagee"] = "bool notNull";
    return $specs;
  }
    
  function updateFormFields() {
		parent::updateFormFields();
    $this->_view = $this->text;
    $this->_shortview = truncate($this->text);
 	}
  
  // Forward references
  function loadRefsFwd() {
    $this->loadRefGroup();
  }
  
  function loadRefGroup() {
    $this->_ref_group = new CGroups();
    $this->_ref_group->load($this->group_id);
  }
  
  // Backward references
  function loadRefsBack() {
    $this->loadRefsUsers();
  }
  
  function loadRefsUsers($type = null) {
    if(!$type) {
      $where = array(
        "function_id" => "= '$this->function_id'"
      );
      $ljoin = array(
        "users" => "`users`.`user_id` = `users_mediboard`.`user_id`"
      );
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
    $where = array(
      "type" => "= 'cabinet'",
      "group_id" => "= '$g'"
    );
    return $this->loadListWithPerms($perm_type, $where, "text");
  }
  
  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_group->fillTemplate($template);
    $template->addProperty("Cabinet - nom"         , $this->text      );
    $template->addProperty("Cabinet - sous-titre"  , $this->soustitre );
    $template->addProperty("Cabinet - adresse"     , $this->adresse   );
    $template->addProperty("Cabinet - cp ville"    , "$this->cp $this->ville");
    $template->addProperty("Cabinet - téléphone"   , $this->tel       );
    $template->addProperty("Cabinet - fax"         , $this->fax       );
  }
}
?>