<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CSenderMLLP 
 * Interoperability Sender MLLP
 */
class CSenderMLLP extends CInteropSender {
  // DB Table key
  public $sender_mllp_id;
  
  public $_duplicate;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sender_mllp';
    $spec->key   = 'sender_mllp_id';

    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["expediteur_ihe"]  = "CExchangeIHE sender_id";
    $backProps["config_hprimxml"] = "CHprimXMLConfig sender_id";
    $backProps["config_hl7"]      = "CHL7Config sender_id";
        
    return $backProps;
  }
  
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources[] = CExchangeSource::get("$this->_guid", "mllp", true, $this->_type_echange, false);
  }
  
  function read() {
    $this->loadRefsExchangesSources();
  }

  /**
   * @see parent::store()
   */
  function store(){
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($this->_duplicate) {
      $duplicate = new self;
      
      foreach ($this->getProperties() as $name => $value) {
        if ($name[0] !== "_" && $name != $this->_spec->key) {
          $duplicate->$name = $value;
        }
      }
      
      $duplicate->nom     .= " (Copy)";
      if ($duplicate->libelle) {
        $duplicate->libelle .= " (Copy)";
      }
      
      $duplicate->store();
    }
    
    $this->_duplicate = null;
  }
}
