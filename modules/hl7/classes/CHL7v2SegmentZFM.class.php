<?php

/**
 * Represents an HL7 ZFM message segment (Mouvement PMSI) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentZFM
 * ZFM - Represents an HL7 ZFM message segment (Mouvement PMSI)
 */

class CHL7v2SegmentZFM extends CHL7v2Segment {
  var $name   = "ZFM";
  
  /**
   * @var CSejour
   */
  var $sejour = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event);
    $sejour = new CSejour();
    $sejour = $this->sejour;
    
    // ZFM-1: Mode d'entrée PMSI
    $data[] = $sejour->mode_entree;
    
    // ZFM-2: Mode de sortie PMSI
    // normal - transfert - mutation - deces
    $mode_sortie = null;
    switch ($sejour->mode_sortie) {
      case "transfert" :
        $mode_sortie = 7;
        break;
      case "mutation" :
        $mode_sortie = 6;
        break;
      case "deces" :
        $mode_sortie = 9;
        break;
      default :
        $mode_sortie = 5;
        break;
    }
    $data[] = $mode_sortie;
    
    // ZFM-3: Mode de provenance PMSI
    $data[] = null;
    
    // ZFM-4: Mode de destination PMSI
    $data[] = null;
    
    $this->fill($data);
  }
}

?>