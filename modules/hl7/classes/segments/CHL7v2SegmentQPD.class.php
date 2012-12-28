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

  /**
   * @var CPatient
   */
  var $patient = null;

  function build(CHL7v2Event $event) {
    parent::build($event);

    $patient = $this->patient;

    // QPD-1: Message Query Name (CE)
    $data[] = "IHE PDQ Query";
    
    // QPD-2: Query Tag (ST)
    $data[] = "PDQPDC.".CHL7v2::getDateTime();
    
    // QPD-3: User Parameters (in successive fields) (Varies)
    $data[] = array(
      // Nom
      $this->addParameters($patient, "nom", "5.1.1"),

      // Prénom
      $this->addParameters($patient, "prenom", "5.2"),

      // Nom de jeune fille
      $this->addParameters($patient, "nom_jeune_fille", "6.1.1"),

      // Date de naissance
      $this->addParameters($patient, "naissance", "7.1"),

      // Ville
      $this->addParameters($patient, "ville", "11.3"),

      // CP
      $this->addParameters($patient, "cp", "11.5"),
    );

    $this->fill($data);
  }

  function addParameters(CPatient $patient, $spec, $field) {
    if (!$patient->$spec) {
      return;
    }

    return array(
      "@PID.$field",
      $patient->$spec
    );
  }
}

?>