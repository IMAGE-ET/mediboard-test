<?php /* $Id: modePaiement.class.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

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
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "user_id"   => "ref|notNull",
      "smic"      => "currency|min|0|notNull",
      "csgds"     => "pct|notNull",
      "csgnds"    => "pct|notNull",
      "ssms"      => "pct|notNull",
      "ssmp"      => "pct|notNull",
      "ssvs"      => "pct|notNull",
      "ssvp"      => "pct|notNull",
      "rcs"       => "pct|notNull",
      "rcp"       => "pct|notNull",
      "agffs"     => "pct|notNull",
      "agffp"     => "pct|notNull",
      "aps"       => "pct|notNull",
      "app"       => "pct|notNull",
      "acs"       => "pct|notNull",
      "acp"       => "pct|notNull",
      "aatp"      => "pct|notNull",
      "nom"       => "str|notNull|confidential",
      "adresse"   => "str|confidential",
      "cp"        => "num|length|5|confidential",
      "ville"     => "str|confidential",
      "siret"     => "num|length|14|confidential",
      "ape"       => "str|length|4|confidential",
      "matricule" => "code|insee|confidential"
    );
    $this->_props =& $props;

    static $seek = array (
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
  }

  // Forward references
  function loadRefsFwd() {
    // user
    $this->_ref_user = new CMediusers();
    $this->_ref_user->load($this->user_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_user->getPerm($permType));
  }
  
  function loadFromUser($user_id) {
    $where = array();
    $where["user_id"] = "= '$user_id'";
    $this->loadObject($where);
  }
}

?>