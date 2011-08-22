<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CParamsPaie Class
 */
class CParamsPaie extends CMbObject {
  // DB Table key
  var $params_paie_id = null;

  // DB Fields
  var $employecab_id = null;
  
  // Fiscalité
  var $smic    = null; // valeur du smic horaire
  var $csgnis  = null; // CSG non imposable salariale
  var $csgds   = null; // CSG déductible salariale
  var $csgnds  = null; // CSG non déductible salariale
  var $ssms    = null; // sécurité sociale maladie salariale
  var $ssmp    = null; // sécurité sociale maladie patronale
  var $ssvs    = null; // sécurité sociale vieillesse salariale
  var $ssvp    = null; // sécurité sociale vieillesse patronale
  var $rcs     = null; // retraite complémentaire salariale
  var $rcp     = null; // retraite complémentaire patronale
  var $agffs   = null; // AGFF salariale
  var $agffp   = null; // AGFF patronale
  var $aps     = null; // assurance prévoyance salariale
  var $app     = null; // assurance prévoyance patronale
  var $acs     = null; // assurance chomage salariale
  var $acp     = null; // assurance chomage patronale
  var $aatp    = null; // assurance accident de travail patronale
  var $csp     = null; // contribution solidarité patronnale
  var $ms      = null; // mutuelle salariale
  var $mp      = null; // mutuelle patronale
  
  // Employeur
  var $nom     = null;
  var $adresse = null;
  var $cp      = null;
  var $ville   = null;
  var $siret   = null;
  var $ape     = null;
  
  // Utilisateur
  var $matricule = null; // numéro de sécurité sociale

  // Object References
  var $_ref_employe = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'params_paie';
    $spec->key   = 'params_paie_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["fiches"] = "CFichePaie params_paie_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["employecab_id"] = "ref notNull class|CEmployeCab";
    $specs["smic"]          = "currency notNull|min|0";
    $specs["csgnis"]        = "pct notNull";
    $specs["csgds"]         = "pct notNull";
    $specs["csgnds"]        = "pct notNull";
    $specs["ssms"]          = "pct notNull";
    $specs["ssmp"]          = "pct notNull";
    $specs["ssvs"]          = "pct notNull";
    $specs["ssvp"]          = "pct notNull";
    $specs["rcs"]           = "pct notNull";
    $specs["rcp"]           = "pct notNull";
    $specs["agffs"]         = "pct notNull";
    $specs["agffp"]         = "pct notNull";
    $specs["aps"]           = "pct notNull";
    $specs["app"]           = "pct notNull";
    $specs["acs"]           = "pct notNull";
    $specs["acp"]           = "pct notNull";
    $specs["aatp"]          = "pct notNull";
    $specs["csp"]           = "pct notNull";
    $specs["ms"]            = "currency notNull min|0";
    $specs["mp"]            = "currency notNull min|0";
    $specs["nom"]           = "str notNull confidential";
    $specs["adresse"]       = "text confidential";
    $specs["cp"]            = "numchar length|5 confidential";
    $specs["ville"]         = "str confidential";
    $specs["siret"]         = "numchar length|14 confidential";
    $specs["ape"]           = "str maxLength|6 confidential";
    $specs["matricule"]     = "code insee confidential mask|9S99S99S9xS999S999S99";
    return $specs;
  }

  // Forward references
  function loadRefsFwd() {
    $this->_ref_employe = new CEmployeCab;
    $this->_ref_employe->load($this->employecab_id);
  }
  
  function getPerm($permType) {
    if (!$this->_ref_employe) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_employe->getPerm($permType));
  }
  
  function loadFromUser($employecab_id) {
    $where = array();
    $where["employecab_id"] = "= '$employecab_id'";
    $this->loadObject($where);
  }
}

?>