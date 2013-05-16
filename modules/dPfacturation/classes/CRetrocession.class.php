<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Permet d'�diter des relances pour les factures impay�es
 */
class CRetrocession extends CMbObject {
  // DB Table key
  public $retrocession_id;
  
  // DB Fields
  public $praticien_id;
  public $nom;
  public $type;
  public $valeur;
  public $pct_pm;
  public $pct_pt;
  public $code_class;
  public $code;
  
  // Distant Field
  public $_montant_total;
  
  // Object References
  public $_ref_praticien;
  public $_ref_acte;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'retrocession';
    $spec->key   = 'retrocession_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["praticien_id"]= "ref notNull class|CMediusers";
    $props["nom"]         = "str notNull";
    $props["type"]        = "enum list|montant|pct|autre default|montant";
    $props["valeur"]      = "float";
    $props["pct_pm"]      = "pct default|0";
    $props["pct_pt"]      = "pct default|0";
    $props["code_class"]  = "enum list|CActeCCAM|CActeNAGP|CActeTarmed|CActeCaisse default|CActeCCAM";
    $props["code"]        = "str";
    
    $props["_montant_total"]  = "currency";
    return $props;
  }
  
  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  

  /**
   * Chargement du praticien de la r�trocession
   * 
   * @return $this->_ref_praticien
   */
  function loadRefPraticien(){
    if (!$this->_ref_praticien) {
      $this->_ref_praticien = $this->loadFwdRef("praticien_id", true);
    }
    return $this->_ref_praticien;
  }
  
  /**
   * Chargement de l'acte correspondant au code
   * 
   * @return $this->_ref_acte
   */
  function loadRefCode(){
    if (!$this->_ref_acte) {
      $this->_ref_acte = new $this->code_class;
      $this->_ref_acte->code = $this->code;
      $this->_ref_acte->updateMontantBase();
    }
    return $this->_ref_acte;
  }
  
  /**
   * Mise � jour du montant total de la r�trocession
   * 
   * @param string $code code pour mettre � jour
   * 
   * @return $this->_ref_acte
   */
  function updateMontant ($code = "") {
    $this->_montant_total = 0;
    if ($code == $this->code || !$code) {
      if ($this->type == "montant") {
        $this->_montant_total = $this->valeur;
      }
      elseif ($this->type == "pct") {
        $this->loadRefCode();
        $this->_ref_acte->updateFormFields();
        $this->_montant_total = $this->_ref_acte->_montant_facture * $this->valeur/100;
      }
      elseif (($this->code_class == "CActeTarmed" || $this->code_class == "CActeCaisse") &&
            CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
        $_code = $this->loadRefCode();
        $tarmed = $_code->_ref_tarmed;
        $pm = $this->code_class == "CActeTarmed" ? $tarmed->tp_al * $tarmed->f_al : $_code->_ref_prestation_caisse->pt_medical;
        $pt = $this->code_class == "CActeTarmed" ? $tarmed->tp_tl * $tarmed->f_tl : $_code->_ref_prestation_caisse->pt_technique;
        
        $this->_montant_total = $pm * $this->pct_pm + $pt * $this->pct_pt;
      }
    }
    $this->_montant_total = round($this->_montant_total, 2);
    return $this->_montant_total;
  }
  
}
