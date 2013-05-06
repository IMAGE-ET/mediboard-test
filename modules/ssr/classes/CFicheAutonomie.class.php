<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CFicheAutonomie extends CMbObject {
  // DB Table key
  public $fiche_autonomie_id;
 
  // DB Fields
  public $sejour_id;
  public $alimentation;
  public $toilette;
  public $habillage_haut;
  public $habillage_bas;
  public $toilettes;
  public $utilisation_toilette;
  public $transfert_lit;
  public $locomotion;
  public $locomotion_materiel;
  public $escalier;
  public $pansement;
  public $escarre;
  public $soins_cutanes;
  public $comprehension;
  public $expression;
  public $memoire;
  public $resolution_pb;
  public $antecedents;
  public $traitements;
  public $etat_psychique;
  public $devenir_envisage;
  
  // Object References
  public $_ref_sejour;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'fiche_autonomie';
    $spec->key   = 'fiche_autonomie_id';
    $spec->uniques["sejour_id"] = array("sejour_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"]      = "ref notNull class|CSejour cascade";
    $specs["alimentation"]   = "enum notNull list|autonome|partielle|totale";
    $specs["toilette"]       = "enum notNull list|autonome|partielle|totale";
    $specs["habillage_haut"] = "enum notNull list|autonome|partielle|totale";
    $specs["habillage_bas"]  = "enum notNull list|autonome|partielle|totale";
    $specs["transfert_lit"]  = "enum notNull list|autonome|partielle|totale";
    $specs["locomotion"]     = "enum notNull list|autonome|partielle|totale";
    $specs["escalier"]       = "enum notNull list|autonome|partielle|totale";
    $specs["toilettes"]      = "enum notNull list|autonome|partielle|totale";
    $specs["utilisation_toilette"]   = "enum list|sonde|couche|bassin|stomie";
    $specs["locomotion_materiel"]    = "enum list|canne|cadre|fauteuil";
    $specs["pansement"]              = "bool notNull";
    $specs["escarre"]                = "bool notNull";
    $specs["soins_cutanes"]          = "text";
    $specs["comprehension"]          = "enum notNull list|intacte|alteree";
    $specs["expression"]             = "enum notNull list|intacte|alteree";
    $specs["memoire"]                = "enum notNull list|intacte|alteree";
    $specs["resolution_pb"]          = "enum notNull list|intacte|alteree";
    $specs["etat_psychique"]         = "text";
    $specs["antecedents"]            = "text";
    $specs["traitements"]            = "text";
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
