<?php /* $Id: evenements.class.php 8931 2010-05-12 12:58:21Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 8931 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLAcquittements extends CHPrimXMLDocument {
  var $_codes_erreurs = array();
    
  static function getAcquittementEvenementXML(CHPrimXMLEvenements $dom_evt) {
    // Message événement patient
    if ($dom_evt instanceof CHPrimXMLEvenementsPatients) {
      return new CHPrimXMLAcquittementsPatients();
    } 
    // Message serveur activité PMSI
    elseif ($dom_evt instanceof CHPrimXMLEvenementsServeurActivitePmsi) {
      return CHPrimXMLAcquittementsServeurActivitePmsi::getEvtAcquittement($dom_evt);
    }
  }
  
  function generateAcquittements($statut, $codes, $commentaires = null, $mbObject = null) {
  }
}
?>