<?php

/**
 * Represents an HL7 ZFP message segment (Situation professionnelle) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentZFP
 * ZFP - Represents an HL7 ZFP message segment (Situation professionnelle)
 */

class CHL7v2SegmentZFP extends CHL7v2Segment {
  var $name   = "ZFP";
  
  /**
   * @var CSejour
   */
  var $sejour = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $sejour = $this->sejour;
    
    // ZFP-1: Activité socio-professionnelle (nomemclature INSEE)
    $data[] = null;
    
    // ZFP-2: Catégorie socio-professionnelle (nomemclature INSEE)
    $data[] = null;
    
    $this->fill($data);
  }
}

?>