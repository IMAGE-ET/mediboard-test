<?php

/**
 * $Id$
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Class CSenderSFTP
 * Interoperability Sender SFTP
 */
class CSenderSFTP extends CInteropSender {
  /**
   * @var integer Primary key
   */
  public $sender_sftp_id;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "sender_sftp";
    $spec->key    = "sender_sftp_id";
    return $spec;  
  }
  
  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["expediteur_hprimxml"] = "CEchangeHprim sender_id";
    $backProps["expediteur_hprim21"]  = "CEchangeHprim21 sender_id";
    $backProps["expediteur_hl7v2"]    = "CExchangeHL7v2 sender_id";
    $backProps["expediteur_phast"]    = "CExchangePhast sender_id";
    $backProps["expediteur_any"]      = "CExchangeAny sender_id";

    $backProps["config_hprimxml"]     = "CHprimXMLConfig sender_id";
    $backProps["config_hl7"]          = "CHL7Config sender_id";
    return $backProps;
  }

  /**
   * @see parent::loadRefsExchangesSources
   */
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources[] = CExchangeSource::get("$this->_guid", "sftp", true, $this->_type_echange, false);
  }

  /**
   * @see parent::read
   */
  function read() {
  }
}