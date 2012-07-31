<?php

/**
 * Represents an HL7 ZBE message segment (Movement) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentZBE
 * ZBE - Represents an HL7 ZBE message segment (Movement)
 */

class CHL7v2SegmentZBE extends CHL7v2Segment {
  static $actions = array(
    "INSERT" => array(
      "A05", "A01", "A14", "A04", "A06", "A07", "A54", "A02", "A15", 
      "A03", "A16", "A21", "A22", "Z80", "Z82", "Z84", "Z86", "Z88"
    ),
    "UPDATE" => array(
      "Z99"
    ),
    "CANCEL" => array(
      "A38", "A11", "A27", /* "A06", "A07", */ "A55", "A12", "A26", "A13", 
      "A25", "A52", "A53", "Z81", "Z83", "Z85", "Z87", "Z89"
    ),
  );
  
  var $name   = "ZBE";
  
  /**
   * @var CSejour
   */
  var $sejour = null;
  
  /**
   * @var CMovement
   */
  var $movement = null;
  
  /**
   * @var CAffectation
   */
  var $curr_affectation = null;
  
  /**
   * @var CAffectation
   */
  var $other_affectation = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $sejour      = $this->sejour;
    $movement    = $this->movement;
    $affectation = $this->curr_affectation;
    if ($this->other_affectation) {
      $affectation = $this->other_affectation;
    }
    
    $action_movement = null;
    foreach (self::$actions as $action => $events) {
      if (in_array($event->code, $events)) {
        $action_movement = $action;
      }
    };
    
    // ZBE-1: Movement ID (EI) (optional)
    $data[] = array (
      array (
        // Entity identifier
        $movement->_view,
        // Autorité assignement
        CAppUI::conf("hl7 assigning_authority_namespace_id"),
        CAppUI::conf("hl7 assigning_authority_universal_id"),
        CAppUI::conf("hl7 assigning_authority_universal_type_id"),
      )
    );
    
    // ZBE-2: Start of Movement Date/Time (TS)
    $data[] = ($action_movement == "UPDATE" || $action_movement == "CANCEL") ? $movement->last_update : $movement->start_of_movement;
    
    // ZBE-3: End of Movement Date/Time (TS) (optional)
    // Forbidden (IHE France)
    $data[] = null;
    
    // ZBE-4: Action on the Movement (ID)
    $data[] = $action_movement;
    
    // ZBE-5: Indicator "Historical Movement" (ID) 
    $data[] = $movement->_current ? "Y" : "N";
    
    // ZBE-6: Original trigger event code (ID) (optional)
    $data[] = ($action_movement == "UPDATE" || $action_movement == "CANCEL") ? $movement->original_trigger_code : null;
    
    $ufs = $sejour->getUF(null, $affectation->_id);
    // ZBE-7: Ward of medical responsibility in the period starting with this movement (XON) (optional)
    $uf_medicale = isset($ufs["medicale"]) ? $ufs["medicale"] : null;
    if (isset($uf_medicale->_id)) {
      $data[] = array(
        array(
          // ZBE-7.1 : Libellé de l'UF
          $uf_medicale->libelle,
          null,
          null,
          null,
          null,
          // ZBE-7.6 : Identifiant de l'autorité d'affectation  qui a attribué l'identifiant de l'UF de responsabilité médicale
          $this->getAssigningAuthority("mediboard"),
          // ZBE-7.7 : La seule valeur utilisable de la table 203 est "UF"
          "UF",
          null,
          null,
          // ZBE-7.10 : Identifiant de l'UF de responsabilité médicale
          $uf_medicale->code
        )
      );
    } 
    else {
      $data[] = null;
    }
    
    // Traitement des segments spécifiques extension PAM
    $this->fillOtherSegments($data, $ufs, $event);
    
    $this->fill($data);
  }

  function fillOtherSegments(&$data, $ufs, CHL7v2Event $event) {}
}

?>