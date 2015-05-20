<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage DICOM
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * A Dicom sender
 */
class CDicomSender extends CInteropSender {
  
  /**
   * Table Key
   * 
   * @var integer
   */
  public $dicom_sender_id = null;
  
  /**
   * Initialize the class specifications
   * 
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "dicom_sender";
    $spec->key    = "dicom_sender_id";
    
    return $spec;
  }
  
  /**
   * Get backward reference specifications
   * 
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["exchange_dicom"] = "CExchangeDicom sender_id";
    $backProps["session_dicom"]  = "CDicomSession sender_id";
    $backProps['config']         = 'CDicomConfig sender_id';

    return $backProps;
  }
  
  /**
   * Get exchanges sources
   * 
   * @return void
   */
  function loadRefsExchangesSources() {
    $source_dicom = CExchangeSource::get("$this->_guid", "dicom", true, $this->_type_echange, false);
    $this->_ref_exchanges_sources[$source_dicom->_guid] = $source_dicom;
  }
}
