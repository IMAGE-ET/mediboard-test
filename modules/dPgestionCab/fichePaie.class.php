<?php /* $Id: modePaiement.class.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

/**
 * The CFichePaie Class
 */
class CFichePaie extends CMbObject {
  // DB Table key
  var $fiche_paie_id = null;

  // DB Fields
  var $params_paie_id = null;
  var $debut          = null;
  var $fin            = null;
  var $salaire        = null;
  var $heures         = null;
  var $heures_sup     = null;
  var $mutuelle       = null;
  var $precarite      = null;
  var $anciennete     = null;
  var $conges_payes   = null;
  var $prime_speciale = null;
  
  // Forms Fields
  var $_salaire_base = null;
  var $_salaire_heures_sup = null;
  var $_prime_precarite = null;
  var $_prime_anciennete = null;
  var $_conges_payes = null;
  var $_salaire_brut = null;
  var $_base_csg = null;
  var $_csgds   = null; // CSG déductible salariale
  var $_csgnds  = null; // CSG non déductible salariale
  var $_ssms    = null; // sécurité sociale maladie salariale
  var $_ssmp    = null; // sécurité sociale maladie patronale
  var $_ssvs    = null; // sécurité sociale vieillesse salariale
  var $_ssvp    = null; // sécurité sociale vieillesse patronale
  var $_rcs     = null; // retraite complémentaire salariale
  var $_rcp     = null; // retraite complémentaire patronale
  var $_agffs   = null; // AGFF salariale
  var $_agffp   = null; // AGFF patronale
  var $_aps     = null; // assurance prévoyance salariale
  var $_app     = null; // assurance prévoyance patronale
  var $_acs     = null; // assurance chomage salariale
  var $_acp     = null; // assurance chomage patronale
  var $_aatp    = null; // assurance accident de travail patronale
  var $_reduc_bas_salaires = null;
  var $_total_retenues = null;
  var $_total_cot_patr = null;
  var $_salaire_a_payer = null;
  var $_salaire_net = null;

  // Object References
  var $_ref_params_paie = null;

  function CFichePaie() {
    $this->CMbObject("fiche_paie", "fiche_paie_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "params_paie_id" => "ref|notNull",
      "debut"          => "date|notNull",
      "fin"            => "date|moreEquals|debut|notNull",
      "salaire"        => "currency|min|0|notNull",
      "heures"         => "num|notNull",
      "heures_sup"     => "num|notNull",
      "mutuelle"       => "currency|min|0|notNull",
      "anciennete"     => "pct|notNull",
      "precarite"      => "pct|notNull",
      "conges_payes"   => "pct|notNull",
      "prime_speciale" => "pct|notNull"
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
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Fiche de paie du ".$this->debut." au ".$this->fin;
    if($this->fiche_paie_id) {
      // On charge cette référence dès le load
      $this->_ref_params_paie = new CParamsPaie();
      $this->_ref_params_paie->load($this->params_paie_id);
      $this->_ref_params_paie->loadRefsFwd();
      $this->_salaire_base = $this->salaire * $this->heures;
      $this->_salaire_brut = $this->_salaire_base;
      $this->_salaire_heures_sup = ($this->salaire * 1.25) * $this->heures_sup;
      $this->_salaire_brut += $this->_salaire_heures_sup;
      $this->_prime_precarite = ($this->precarite / 100) *
                                ($this->_salaire_base + $this->_salaire_heures_sup);
      $this->_salaire_brut += $this->_prime_precarite;
      $this->_prime_anciennete = ($this->anciennete / 100) *
                                 ($this->_salaire_base + $this->_salaire_heures_sup);
      $this->_salaire_brut += $this->_prime_anciennete;
      $this->_conges_payes = ($this->conges_payes / 100) *
                                   ($this->_salaire_base +
                                    $this->_salaire_heures_sup +
                                    $this->_prime_precarite +
                                    $this->_prime_anciennete);
      $this->_salaire_brut += $this->_conges_payes;
      $this->_salaire_brut += $this->prime_speciale;
      $this->_ssms    = $this->_salaire_brut * $this->_ref_params_paie->ssms / 100;
      $this->_total_retenues = $this->_ssms;
      $this->_ssmp    = $this->_salaire_brut * $this->_ref_params_paie->ssmp / 100;
      $this->_total_cot_patr = $this->_ssmp;
      $this->_ssvs    = $this->_salaire_brut * $this->_ref_params_paie->ssvs / 100;
      $this->_total_retenues += $this->_ssvs;
      $this->_ssvp    = $this->_salaire_brut * $this->_ref_params_paie->ssvp / 100;
      $this->_total_cot_patr += $this->_ssvp;
      $this->_rcs     = $this->_salaire_brut * $this->_ref_params_paie->rcs / 100;
      $this->_total_retenues += $this->_rcs;
      $this->_rcp     = $this->_salaire_brut * $this->_ref_params_paie->rcp / 100;
      $this->_total_cot_patr += $this->_rcp;
      $this->_agffs   = $this->_salaire_brut * $this->_ref_params_paie->agffs / 100;
      $this->_total_retenues += $this->_agffs;
      $this->_agffp   = $this->_salaire_brut * $this->_ref_params_paie->agffp / 100;
      $this->_total_cot_patr += $this->_agffp;
      $this->_aps     = $this->_salaire_brut * $this->_ref_params_paie->aps / 100;
      $this->_total_retenues += $this->_aps;
      $this->_app     = $this->_salaire_brut * $this->_ref_params_paie->app / 100;
      $this->_total_cot_patr += $this->_app;
      // On peut calculer ici la CSG/RDS
      $this->_base_csg = $this->_salaire_brut * 0.97 + $this->_app;
      $this->_csgds   = $this->_base_csg * $this->_ref_params_paie->csgds / 100;
      $this->_total_retenues += $this->_csgds;
      $this->_csgnds  = $this->_base_csg * $this->_ref_params_paie->csgnds / 100;
      $this->_total_retenues += $this->_csgnds;
      // On reviens à nos cotisations classiques
      $this->_acs     = $this->_salaire_brut * $this->_ref_params_paie->acs / 100;
      $this->_total_retenues += $this->_acs;
      $this->_acp     = $this->_salaire_brut * $this->_ref_params_paie->acp / 100;
      $this->_total_cot_patr += $this->_acp;
      $this->_aatp    = $this->_salaire_brut * $this->_ref_params_paie->aatp / 100;
      $this->_total_cot_patr += $this->_aatp;
      $this->_reduc_bas_salaires = (0.26/0.6)*(1.6*($this->_ref_params_paie->smic*$this->heures/$this->_salaire_brut)-1);
      $this->_reduc_bas_salaires = min(0.26, $this->_reduc_bas_salaires)*$this->_salaire_brut;
      $this->_reduc_bas_salaires = max(0, $this->_reduc_bas_salaires);
      $this->_total_cot_patr -= $this->_reduc_bas_salaires;
      $this->_total_retenues += $this->mutuelle;
      $this->_salaire_a_payer = $this->_salaire_brut - $this->_total_retenues;
      $this->_salaire_net = $this->_salaire_a_payer + $this->_csgnds;
    }
  }
  
  function loadRefsFwd() {
    $this->_ref_params_paie = new CParamsPaie;
    $this->_ref_params_paie->load($this->params_paie_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_params_paie) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_params_paie->getPerm($permType));
  }
}

?>