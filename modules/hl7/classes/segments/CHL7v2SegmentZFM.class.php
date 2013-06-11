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

  /** @var string */
  public $name   = "ZFM";
  

  /** @var CSejour */
  public $sejour;

  /**
   * Build ZFM segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $sejour = $this->sejour;
    
    // ZFM-1: Mode d'entrée PMSI
    $data[] = $sejour->mode_entree;
    
    // ZFM-2: Mode de sortie PMSI
    // normal - transfert - mutation - deces
    $data[] = $this->getModeSortie($sejour);
    
    // ZFM-3: Mode de provenance PMSI
    $data[] = $this->getModeProvenance($sejour);
    
    // ZFM-4: Mode de destination PMSI
    $data[] = $sejour->destination;
    
    $this->fill($data);
  }
}