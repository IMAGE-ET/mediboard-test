<?php

/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 16236 $
 */

/**
 * Class CHPrimXMLAcquittements
 */
class CHPrimXMLAcquittements extends CHPrimXMLDocument {
  public $_codes_erreurs = array();

  /**
   * Get acknowledgment event
   *
   * @param CHPrimXMLEvenements $dom_evt Event
   *
   * @return CHPrimXMLAcquittementsFraisDivers|CHPrimXMLAcquittementsPatients|CHPrimXMLAcquittementsPmsi|
   * CHPrimXMLAcquittementsServeurActes|CHPrimXMLAcquittementsServeurIntervention|null
   */
  static function getAcquittementEvenementXML(CHPrimXMLEvenements $dom_evt) {
    // Message événement patient
    if ($dom_evt instanceof CHPrimXMLEvenementsPatients) {
      return new CHPrimXMLAcquittementsPatients();
    } 

    // Message serveur activité PMSI
    if ($dom_evt instanceof CHPrimXMLEvenementsServeurActivitePmsi) {
      return CHPrimXMLAcquittementsServeurActivitePmsi::getEvtAcquittement($dom_evt);
    }

    return null;
  }

  /**
   * Generate acknowledgement
   *
   * @param string $statut       Status code
   * @param array  $codes        Codes
   * @param string $commentaires Comments
   * @param string $mbObject     Object
   *
   * @return string
   */
  function generateAcquittements($statut, $codes, $commentaires = null, $mbObject = null) {
  }
}
