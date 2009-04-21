<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
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
  var $heures_comp    = null;
  var $heures_sup     = null;
  var $precarite      = null;
  var $anciennete     = null;
  var $conges_payes   = null;
  var $prime_speciale = null;
  
  var $final_file     = null;
  
  // Forms Fields
  var $_salaire_base = null;
  var $_salaire_heures_comp = null;
  var $_salaire_heures_sup  = null;
  var $_total_heures = null;
  var $_prime_precarite = null;
  var $_prime_anciennete = null;
  var $_conges_payes = null;
  var $_salaire_brut = null;
  var $_base_csg = null;
  var $_csgnis  = null; // CSG non imposable salariale
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
  var $_csp     = null; // contribution solidarité patronale
  var $_reduc_bas_salaires = null;
  var $_total_retenues     = null;
  var $_total_cot_patr     = null;
  var $_salaire_a_payer    = null;
  var $_salaire_net        = null;


  // Object References
  var $_ref_params_paie = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'fiche_paie';
    $spec->key   = 'fiche_paie_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["params_paie_id"] = "ref notNull class|CParamsPaie";
    $specs["debut"]          = "date notNull";
    $specs["fin"]            = "date notNull moreEquals|debut";
    $specs["salaire"]        = "currency notNull min|0";
    $specs["heures"]         = "num notNull max|255";
    $specs["heures_comp"]    = "num notNull max|255";
    $specs["heures_sup"]     = "num notNull max|255";
    $specs["anciennete"]     = "pct notNull";
    $specs["precarite"]      = "pct notNull";
    $specs["conges_payes"]   = "pct notNull";
    $specs["prime_speciale"] = "currency notNull min|0";
    $specs["final_file"]     = "html";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_locked = ($this->final_file !== null);
    $this->_view = "Fiche de paie du ".mbTransformTime(null, $this->debut, CAppUI::conf("date"))." au ".mbTransformTime(null, $this->fin, CAppUI::conf("date"));
    if($this->fiche_paie_id) {
      // On charge cette référence dès le load
      $this->_ref_params_paie = new CParamsPaie();
      $this->_ref_params_paie->load($this->params_paie_id);
      $this->_ref_params_paie->loadRefsFwd();
      $this->_total_heures        = $this->heures + $this->heures_comp + $this->heures_sup;
      $this->_salaire_base        = $this->salaire * $this->heures;
      $this->_salaire_brut        = $this->_salaire_base;
      $this->_salaire_heures_comp = $this->salaire * $this->heures_comp;
      $this->_salaire_brut       += $this->_salaire_heures_comp;
      $this->_salaire_heures_sup  = ($this->salaire * 1.25) * $this->heures_sup;
      $this->_salaire_brut       += $this->_salaire_heures_sup;
      $this->_total_heures_sup    = $this->_salaire_heures_comp + $this->_salaire_heures_sup;
      $this->_prime_precarite     = ($this->precarite / 100) *
                                    ($this->_salaire_base + $this->_total_heures_sup);
      $this->_salaire_brut       += $this->_prime_precarite;
      $this->_prime_anciennete    = ($this->anciennete / 100) *
                                    ($this->_salaire_base + $this->_total_heures_sup);
      $this->_salaire_brut       += $this->_prime_anciennete;
      $this->_conges_payes        = ($this->conges_payes / 100) *
                                    ($this->_salaire_base +
                                     $this->_total_heures_sup +
                                     $this->_prime_precarite +
                                     $this->_prime_anciennete);
      $this->_salaire_brut       += $this->_conges_payes;
      $this->_salaire_brut       += $this->prime_speciale;
      $this->_ssms                = $this->_salaire_brut * $this->_ref_params_paie->ssms / 100;
      $this->_total_retenues      = $this->_ssms;
      $this->_ssmp                = $this->_salaire_brut * $this->_ref_params_paie->ssmp / 100;
      $this->_total_cot_patr      = $this->_ssmp;
      $this->_ssvs                = $this->_salaire_brut * $this->_ref_params_paie->ssvs / 100;
      $this->_total_retenues     += $this->_ssvs;
      $this->_ssvp                = $this->_salaire_brut * $this->_ref_params_paie->ssvp / 100;
      $this->_total_cot_patr     += $this->_ssvp;
      $this->_rcs                 = $this->_salaire_brut * $this->_ref_params_paie->rcs / 100;
      $this->_total_retenues     += $this->_rcs;
      $this->_rcp                 = $this->_salaire_brut * $this->_ref_params_paie->rcp / 100;
      $this->_total_cot_patr     += $this->_rcp;
      $this->_agffs               = $this->_salaire_brut * $this->_ref_params_paie->agffs / 100;
      $this->_total_retenues     += $this->_agffs;
      $this->_agffp               = $this->_salaire_brut * $this->_ref_params_paie->agffp / 100;
      $this->_total_cot_patr     += $this->_agffp;
      $this->_aps                 = $this->_salaire_brut * $this->_ref_params_paie->aps / 100;
      $this->_total_retenues     += $this->_aps;
      $this->_app                 = $this->_salaire_brut * $this->_ref_params_paie->app / 100;
      $this->_total_cot_patr     += $this->_app;
      // On peut calculer ici la CSG/RDS
      $this->_base_csgnis     = ($this->_salaire_brut
                                 - $this->_salaire_heures_sup
                                 - $this->_salaire_heures_comp
                                 + $this->_app + $this->_ref_params_paie->mp) * 0.97;
      $this->_csgnis          = $this->_base_csgnis * $this->_ref_params_paie->csgnis / 100;
      $this->_total_retenues += $this->_csgnis;
      $this->_base_csgnds     = $this->_base_csgnis;
      $this->_csgnds          = $this->_base_csgnds * $this->_ref_params_paie->csgnds / 100;
      $this->_total_retenues += $this->_csgnds;
      $this->_base_csgds      = ($this->_total_heures_sup) * 0.97;
      $this->_csgds           = $this->_base_csgds * $this->_ref_params_paie->csgds / 100;
      $this->_total_retenues += $this->_csgds;
      // On reviens à nos cotisations classiques
      $this->_acs             = $this->_salaire_brut * $this->_ref_params_paie->acs / 100;
      $this->_total_retenues += $this->_acs;
      $this->_acp             = $this->_salaire_brut * $this->_ref_params_paie->acp / 100;
      $this->_total_cot_patr += $this->_acp;
      $this->_aatp            = $this->_salaire_brut * $this->_ref_params_paie->aatp / 100;
      $this->_total_cot_patr += $this->_aatp;
      $this->_csp             = $this->_salaire_brut * $this->_ref_params_paie->csp / 100;
      $this->_total_cot_patr += $this->_csp;
      // Mutuelle
      $this->_total_retenues += $this->_ref_params_paie->ms;
      $this->_total_cot_patr += $this->_ref_params_paie->mp;
      // Réductions bas salaires
      $this->_reduc_bas_salaires = (0.281/0.6) * (1.6 * ($this->_ref_params_paie->smic * $this->heures / $this->_salaire_brut) - 1);
      $this->_reduc_bas_salaires = min(0.281, $this->_reduc_bas_salaires) * $this->_salaire_brut;
      $this->_reduc_bas_salaires = max(0, $this->_reduc_bas_salaires);
      $this->_total_cot_patr    -= $this->_reduc_bas_salaires;
      // Défiscalisation des heures sup
      $this->_reduc_heures_sup_sal = $this->_total_heures_sup * 0.215;
      $this->_total_retenues      -= $this->_reduc_heures_sup_sal;
      $this->_reduc_heures_sup_pat = $this->heures_sup * 1.5;
      $this->_total_cot_patr      -= $this->_reduc_heures_sup_pat;
      $this->_salaire_a_payer      = $this->_salaire_brut - $this->_total_retenues;
      $this->_salaire_net          = $this->_salaire_a_payer + $this->_csgnds - $this->_total_heures_sup;
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