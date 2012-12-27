<?php

/**
 * Represents an HL7 QPD message segment (Query Parameter Definition) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentQPD
 * QPD - Represents an HL7 QPD message segment (Query Parameter Definition)
 */

class CHL7v2SegmentQPD extends CHL7v2Segment {
  var $name   = "QPD";


  function build(CHL7v2Event $event) {
    parent::build($event);

    // QPD-1: Message Query Name (CE)
    $data[] = "IHE PDQ Query";
    
    // QPD-2: Query Tag (ST)
    $data[] = "PDQPDC.".mbTransformTime(null, null, "%Y%m%d%H%M%S");
    
    // QPD-3: User Parameters (in successive fields) (Varies)
    $data[] = array(

    );

    $this->fill($data);
  }
}

?>