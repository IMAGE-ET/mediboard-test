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
 * Liaison entre les éléments facturable et leur facture
 */
class CFactureLiaison extends CMbMetaObject {

  // DB Table key
  public $facture_liaison_id;
  
  // DB Fields
  public $facture_id;
  public $facture_class;
  public $object_id;
  public $object_class;
  
  // Object References
  public $_ref_facture;
  public $_ref_facturable;
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_liaison';
    $spec->key   = 'facture_liaison_id';
    return $spec;
  }
    
  /**
   * getBackProps
   * 
   * @return $backProps
  **/
  function getBackProps() {
    $backProps = parent::getBackProps();
    return $backProps;
  }
   
  /**
   * getProps
   * 
   * @return $props
  **/
  function getProps() {
    $specs = parent::getProps();
    $specs["facture_id"]        = "num notNull";
    $specs["facture_class"]     = "enum notNull list|CFactureCabinet|CFactureEtablissement show|0 default|CFactureCabinet";
    $specs["object_id"]         = "ref notNull class|CFacturable meta|object_class";
    return $specs;
  }
     
  /**
   * Chargement de la facture
   * 
   * @param bool $cache cache
   * 
   * @return $this->_ref_facture
  **/
  function loadRefFacture($cache = 1) {
    $this->_ref_facture = new $this->facture_class;
    $this->_ref_facture->facture_id = $this->facture_id;
    $this->_ref_facture->loadMatchingObject();
    return $this->_ref_facture;
  }
     
  /**
   * Chargement de l'objet facturable
   * 
   * @return void
  **/
  function loadRefFacturable() {
    return $this->_ref_facturable =  $this->loadTargetObject();
  }
  
  /**
   * Redéfinition du store
   * 
   * @return void
  **/
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    
    $this->loadRefFacture();
  } 
}