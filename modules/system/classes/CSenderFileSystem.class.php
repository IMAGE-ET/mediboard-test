<?php
/**
 * Interop Sender File System
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Class CSenderFileSystem
 * Interoperability Sender File System
 */
class CSenderFileSystem extends CInteropSender {
  // DB Table key
  public $sender_file_system_id;
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sender_file_system';
    $spec->key   = 'sender_file_system_id';

    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["expediteur_hprimxml"] = "CEchangeHprim sender_id";
    $backProps["expediteur_hprim21"]  = "CEchangeHprim21 sender_id";
    $backProps["expediteur_hl7v2"]    = "CExchangeHL7v2 sender_id";
    $backProps["expediteur_dmp"]      = "CExchangeDMP sender_id";
    $backProps["expediteur_phast"]    = "CExchangePhast sender_id";
    $backProps["expediteur_any"]      = "CExchangeAny sender_id";
    
    $backProps["config_hprimxml"]     = "CHprimXMLConfig sender_id";
    $backProps["config_hl7"]          = "CHL7Config sender_id";
        
    return $backProps;
  }
  
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources[] = CExchangeSource::get("$this->_guid", "file_system", true, $this->_type_echange, false);
  }
  
  function read() {
    $this->loadRefsExchangesSources();
  }
}
