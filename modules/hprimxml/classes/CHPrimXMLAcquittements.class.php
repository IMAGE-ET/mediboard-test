<?php /* $Id: evenements.class.php 8931 2010-05-12 12:58:21Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 8931 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "CHPrimXMLDocument");

class CHPrimXMLAcquittements extends CHPrimXMLDocument {  
  static function getAcquittementEvenementXML(CHPrimXMLEvenements $dom_evenement) {
    // Message événement patient
    if ($dom_evenement instanceof CHPrimXMLEvenementsPatients) {
      return new CHPrimXMLAcquittementsPatients();
    } 
    // Message serveur activité PMSI
    elseif ($dom_evenement instanceof CHPrimXMLEvenementsServeurActivitePmsi) {
      return new CHPrimXMLAcquittementsServeurActivitePmsi();
    }
  }
  
  function generateAcquittements($statut, $codes, $commentaires = null, $mbObject = null) {}
}
?>