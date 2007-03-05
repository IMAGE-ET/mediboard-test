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
  var $employecab_id = null;
  
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
  }

  function getSpecs() {
    return array (
      "employecab_id" => "notNull ref",
      "smic"          => "notNull currency|min|0",
      "csgds"         => "notNull pct",
      "csgnds"        => "notNull pct",
      "ssms"          => "notNull pct",
      "ssmp"          => "notNull pct",
      "ssvs"          => "notNull pct",
      "ssvp"          => "notNull pct",
      "rcs"           => "notNull pct",
      "rcp"           => "notNull pct",
      "agffs"         => "notNull pct",
      "agffp"         => "notNull pct",
      "aps"           => "notNull pct",
      "app"           => "notNull pct",
      "acs"           => "notNull pct",
      "acp"           => "notNull pct",
      "aatp"          => "notNull pct",
      "nom"           => "notNull str confidential",
      "adresse"       => "str confidential",
      "cp"            => "numchar length|5 confidential",
      "ville"         => "str confidential",
      "siret"         => "numchar length|14 confidential",
      "ape"           => "str length|4 confidential",
      "matricule"     => "code insee confidential"
    );
  }

  // Forward references
  function loadRefsFwd() {
    $this->_ref_employe = new CEmployeCab;
    $this->_ref_employe->load($this->employecab_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_user->getPerm($permType));
  }
  
  function loadFromUser($employecab_id) {
    $where = array();
    $where["employecab_id"] = "= '$employecab_id'";
    $this->loadObject($where);
  }
}

?>