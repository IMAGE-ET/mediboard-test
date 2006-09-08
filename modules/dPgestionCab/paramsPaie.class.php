<?php /* $Id: modePaiement.class.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("mbobject"));

require_once($AppUI->getModuleClass("mediusers"));

/**
 * The CParamsPaie Class
 */
class CParamsPaie extends CMbObject {
  // DB Table key
  var $params_paie_id = null;

  // DB Fields
  var $user_id = null;
  
  // Fiscalité
  var $smic    = null; // valeur du smic horaire
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
  var $_ref_user = null;

  function CParamsPaie() {
    $this->CMbObject("params_paie", "params_paie_id");
    
    $this->_props["user_id"]   = "ref|notNull";
    $this->_props["smic"]      = "currency|min|0|notNull";
    $this->_props["csgds"]     = "pct|notNull";
    $this->_props["csgnds"]    = "pct|notNull";
    $this->_props["ssms"]      = "pct|notNull";
    $this->_props["ssmp"]      = "pct|notNull";
    $this->_props["ssvs"]      = "pct|notNull";
    $this->_props["ssvp"]      = "pct|notNull";
    $this->_props["rcs"]       = "pct|notNull";
    $this->_props["rcp"]       = "pct|notNull";
    $this->_props["agffs"]     = "pct|notNull";
    $this->_props["agffp"]     = "pct|notNull";
    $this->_props["aps"]       = "pct|notNull";
    $this->_props["app"]       = "pct|notNull";
    $this->_props["acs"]       = "pct|notNull";
    $this->_props["acp"]       = "pct|notNull";
    $this->_props["aatp"]      = "pct|notNull";
    $this->_props["nom"]       = "str|notNull|confidential";
    $this->_props["adresse"]   = "str|confidential";
    $this->_props["cp"]        = "num|length|5|confidential";
    $this->_props["ville"]     = "str|confidential";
    $this->_props["siret"]     = "num|length|14|confidential";
    $this->_props["ape"]       = "str|length|4|confidential";
    $this->_props["matricule"] = "code|insee|confidential";

    $this->buildEnums();
  }

  // Forward references
  function loadRefsFwd() {
    // user
    $this->_ref_user = new CMediusers();
    $this->_ref_user->load($this->user_id);
  }
  
  function canRead($withRefs = true) {
    if($withRefs) {
      $this->loadRefsFwd();
    }
    $this->_canRead = $this->_ref_user->canRead();
    return $this->_canRead;
  }

  function canEdit($withRefs = true) {
    if($withRefs) {
      $this->loadRefsFwd();
    }
    $this->_canEdit = $this->_ref_user->canEdit();
    return $this->_canEdit;
  }
  
  function loadFromUser($user_id) {
    $where = array();
    $where["user_id"] = "= '$user_id'";
    $this->loadObject($where);
  }
}

?>