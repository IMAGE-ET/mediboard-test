<?php
/**
 * $Id: CHL7v2RecordObservationResultSet.class.php 16357 2012-08-10 08:18:37Z lryo $
 * 
 * @package    Mediboard
 * @subpackage hprim21
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 16357 $
 */

/**
 * Class CHprim21RecordPayment 
 * Record payment, message XML
 */
class CHprim21RecordFiles extends CHPrim21MessageXML {
  function getContentNodes() {
    $data = array();

    $this->queryNodes("//P"  , null, $data, true); // get ALL the REG segments


    $this->queryNodes("//OBX", null, $data, true); // get ALL the REG segments

    return $data;
  }
 
  function handle($ack, CMbObject $object, $data) {

    // foreach ($data["P"] as $node)


  }
}