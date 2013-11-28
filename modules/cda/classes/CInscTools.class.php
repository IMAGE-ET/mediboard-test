<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Outils pour l'INSC
 */
class CInscTools {

  /**
   * Formate la chaine pour l'INSC
   *
   * @param String $string String
   *
   * @return String
   */
  static function formatString($string) {
    $String_no_accent = CMbString::removeAccents($string);
    $string_char = preg_replace(array("/NBSP/","/\(c\)/","/\(r\)/"), " ", $String_no_accent);
    $normalize   = preg_replace("/([^A-Za-z])/", " ", $string_char);
    return mb_strtoupper($normalize);
  }

  /**
   * Create INSC
   *
   * @param CPatient $patient patient
   *
   * @return null|string
   */
  static function createINSC (CPatient $patient) {
    if (!$patient->_vitale_nir_certifie) {
      return "Ce patient ne possèdent pas de numéro de sécurité sociale qui lui est propre";
    }

    list($nir_carte, $nir_carte_key) = explode(" ", $patient->_vitale_nir_certifie);

    $name_carte     = mb_strtoupper(CMbString::removeAccents($patient->_vitale_lastname));
    $prenom_carte   = mb_strtoupper(CMbString::removeAccents($patient->_vitale_firstname));
    $name_patient   = mb_strtoupper(CMbString::removeAccents($patient->nom));
    $prenom_patient = mb_strtoupper(CMbString::removeAccents($patient->prenom));

    if ($name_carte !== $name_patient || $prenom_carte !== $prenom_patient) {
      return "Le bénéficiaire de la carte vitale ne correspond pas au patient en cours";
    }

    $firstName = CInscTools::formatString($patient->_vitale_firstname);
    $insc      = CPatient::calculInsc($nir_carte, $nir_carte_key, $firstName, $patient->_vitale_birthdate);

    if (strlen($insc) !== 22) {
      return "Problème lors du calcul de l'INSC";
    }

    if (!$insc) {
      return "Impossible de calculer l'INSC";
    }

    $last_ins = $patient->loadLastINS();

    if ($last_ins && $last_ins->ins === $insc) {
      return null;
    }

    $ins = new CINSPatient();
    $ins->patient_id = $patient->_id;
    $ins->ins        = $insc;

    $ins->type       = "C";
    $ins->date       = "now";
    $ins->provider   = "Mediboard";


    if ($msg = $ins->store()) {
      return $msg;
    };

    return null;
  }
}