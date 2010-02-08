<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CFicheAutonomie extends CMbObject {
  // DB Table key
  var $fiche_autonomie_id = null;
 
  // DB Fields
  var $sejour_id              = null;
  var $alimentation           = null;
  var $toilette               = null;
  var $habillage_haut         = null;
  var $habillage_bas          = null;
  var $utilisation_toilette   = null;
  var $transfert_lit          = null;
  var $locomotion             = null;
  var $locomotion_materiel    = null;
  var $escalier               = null;
  var $pansement              = null;
  var $escarre                = null;
  var $soins_cutanes          = null;
  var $comprehension          = null;
  var $expression             = null;
  var $memoire                = null;
  var $resolution_pb          = null;
  var $etat_psychique         = null;
  var $devenir_envisage       = null;
  
  // Object References
  var $_ref_sejour    = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'fiche_autonomie';
    $spec->key   = 'fiche_autonomie_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"]      = "ref notNull class|CSejour cascade";
    $specs["alimentation"]   = "enum list|autonome|partielle|totale";
    $specs["toilette"]       = "enum list|autonome|partielle|totale";
    $specs["habillage_haut"] = "enum list|autonome|partielle|totale";
    $specs["habillage_bas"]  = "enum list|autonome|partielle|totale";
    $specs["transfert_lit"]  = "enum list|autonome|partielle|totale";
    $specs["locomotion"]     = "enum list|autonome|partielle|totale";
    $specs["escalier"]       = "enum list|autonome|partielle|totale";
    $specs["utilisation_toilette"]   = "enum list|sonde|couche|bassin|stomie";
    $specs["locomotion_materiel"]    = "enum list|canne|cadre|fauteuil";
    $specs["pansement"]              = "bool";
    $specs["escarre"]                = "bool";
    $specs["soins_cutanes"]          = "text";
    $specs["comprehension"]          = "enum list|intacte|alteree";
    $specs["expression"]             = "enum list|intacte|alteree";
    $specs["memoire"]                = "enum list|intacte|alteree";
    $specs["resolution_pb"]          = "enum list|intacte|alteree";
    $specs["etat_psychique"]         = "text";
    $specs["devenir_envisage"]       = "text";

    return $specs;
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefSejour();
  }
  
  function loadRefSejour() {
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour->load($this->sejour_id);
    $this->_ref_sejour->loadRefsFwd();
  }
}

?>