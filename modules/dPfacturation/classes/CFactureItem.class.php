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
 * Les items d'une facture
 *
 */
class CFactureItem extends CMbMetaObject {
  // DB Table key
  public $factureitem_id;
  
  // DB Fields
  public $object_id;
  public $object_class;
  public $date;
  public $libelle;
  public $code;
  public $type;
  public $montant_base;
  public $montant_depassement;
  public $reduction;
  public $quantite;
  public $coeff;
  public $pm;
  public $pt;
  public $coeff_pm;
  public $coeff_pt;
  public $use_tarmed_bill;
  public $code_ref;
  public $code_caisse;
  public $seance;
  public $forfait;

  // References
  public $_ref_facture;
  public $_montant_facture;
  public $_montant_total_base;
  public $_montant_total_depassement;
   
  public $_ttc;
  
  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'factureitem';
    $spec->key   = 'factureitem_id';
    return $spec;
  }
  
  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["object_id"] = "ref notNull class|CFacture meta|object_class";
    $specs["date"]      = "date notNull";
    $specs["libelle"]   = "text notNull";
    $specs["code"]      = "text notNull";
    $specs["type"]      = "enum notNull list|CActeNGAP|CFraisDivers|CActeCCAM|CActeTarmed|CActeCaisse default|CActeCCAM";
    $specs["montant_base"]        = "currency notNull";
    $specs["montant_depassement"] = "currency";
    $specs["reduction"]  = "currency";
    $specs["quantite"]  = "num notNull";
    $specs["coeff"]     = "currency notNull";
    $specs["pm"]        = "currency";
    $specs["pt"]        = "currency";
    $specs["coeff_pm"]  = "currency";
    $specs["coeff_pt"]  = "currency";
    $specs["use_tarmed_bill"] = "bool default|0";
    $specs["code_ref"]        = "text";
    $specs["code_caisse"]     = "text";
    $specs["seance"]          = "num";
    $specs["forfait"]         = "bool default|0";
    return $specs;
  }
  
  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_montant_facture = $this->montant_base + $this->montant_depassement;
    $this->_view = $this->libelle;
    if ($this->type == "CActeNGAP") {
      $this->_montant_total_base        = $this->montant_base;
      $this->_montant_total_depassement = $this->montant_depassement;
    }
    else {
      $this->_montant_total_base        = $this->montant_base * $this->quantite * $this->coeff;
      $this->_montant_total_depassement = $this->montant_depassement * $this->quantite * $this->coeff;
    }
  }
  
  /**
   * Chargement de la facture
   * 
   * @return void
  **/
  function loadRefFacture(){
    return $this->loadTargetObject();
  }
}
