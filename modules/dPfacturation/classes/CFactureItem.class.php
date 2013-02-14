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
  var $factureitem_id = null;
  
  // DB Fields
  var $object_id = null;
  var $object_class = null;
  var $date       = null;
  var $libelle    = null;
  var $code       = null;
  var $type       = null;
  var $montant_base        = null;
  var $montant_depassement = null;
  var $reduction 	= null;
  var $quantite 	= null;
  var $coeff 	    = null;
  var $pm 	      = null;
  var $pt 	      = null;
  var $coeff_pm 	= null;
  var $coeff_pt 	= null;
  var $use_tarmed_bill 	= null;
  var $code_ref 	= null;
  var $code_caisse 	= null;
  
  // References
  var $_ref_facture = null;
  var $_ref_facture_catalogue_item = null;
   
  var $_ttc = null;
  
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'factureitem';
    $spec->key   = 'factureitem_id';
    return $spec;
  }
  
  /**
   * getProps
   * 
   * @return $props
  **/
  function getProps() {
    $specs = parent::getProps();
    $specs["object_id"] = "ref notNull class|CFacture meta|object_class";
    $specs["date"]      = "date notNull";
    $specs["libelle"]   = "text notNull";
    $specs["code"]      = "text notNull";
    $specs["type"]      = "enum notNull list|CActeNGAP|CFraisDivers|CActeCCAM|CActeTarmed|CActeCaisse default|CActeCCAM";
    $specs["montant_base"]        = "currency notNull";
    $specs["montant_depassement"] = "currency";
    $specs["reduction"]	= "currency";
    $specs["quantite"]  = "num notNull";
    $specs["coeff"]     = "currency notNull";
    $specs["pm"]        = "currency";
    $specs["pt"]        = "currency";
    $specs["coeff_pm"]  = "currency";
    $specs["coeff_pt"]  = "currency";
    $specs["use_tarmed_bill"] = "bool default|0";
    $specs["code_ref"]    = "text";
    $specs["code_caisse"] = "text";
    return $specs;
  }
  
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
  }
  
  /**
   * loadRefsFwd
   * 
   * @return void
  **/
  function loadRefsFwd(){ 
    $this->_ref_facture = new CFacture;
    $this->_ref_facture->load($this->facture_id);
  }
}
?>