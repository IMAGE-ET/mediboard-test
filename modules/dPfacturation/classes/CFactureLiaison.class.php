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
  var $facture_liaison_id = null;
  
  // DB Fields
  var $facture_id       = null;
  var $facture_class    = null;
  var $object_id    = null;
  var $object_class = null;
  
  // Object References
  var $_ref_facture     = null;
  var $_ref_facturable  = null;
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
   * @return void
  **/
  function loadRefFacture($cache = 1) {
    $facture = new $this->facture_class;
    $facture->facture_id = $this->facture_id;
    $facture->loadMatchingObject();
    return $this->_ref_facture = $facture;
  }
     
  /**
   * Chargement de l'objet facturable
   * 
   * @param bool $cache cache
   * 
   * @return void
  **/
  function loadRefFacturable($cache = 1) {
    return $this->_ref_facturable =  $this->loadFwdRef("facturable_id", $cache);
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
    
    $facture = $this->loadRefFacture();
    $facture->updateMontantsFacture();
  } 
}