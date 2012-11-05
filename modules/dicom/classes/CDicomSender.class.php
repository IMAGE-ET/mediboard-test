<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
    $spec->table	= "dicom_sender";
    $spec->key		= "dicom_sender_id";
    
    return $spec;	
  }
  
  /**
   * Get backward reference specifications
   * 
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["echanges_dicom"]   = "CDicomExchange sender_id";

    return $backProps;
  }
  
  /**
   * Get exchanges sources
   * 
   * @return void
   */
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources[] = CExchangeSource::get("$this->_guid", "dicom", true, $this->_type_echange, false);
  }
}
?>