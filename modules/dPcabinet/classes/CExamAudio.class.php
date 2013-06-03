<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Audiogramme associé à une consultation
 */
class CExamAudio extends CMbObject {
  static $frequences = array("125Hz", "250Hz", "500Hz", "1kHz", "2kHz", "4kHz", "8kHz", "16kHz");
  static $pressions = array(-400, -300, -200, -100, 0, 100, 200, 300);

  // DB Table key
  public $examaudio_id;

  // DB References
  public $consultation_id;

  // DB fields
  public $remarques;

  public $gauche_aerien;
  public $gauche_osseux;
  public $gauche_conlat;
  public $gauche_ipslat;
  public $gauche_pasrep;
  public $gauche_vocale;
  public $gauche_tympan;

  public $droite_aerien;
  public $droite_osseux;
  public $droite_conlat;
  public $droite_ipslat;
  public $droite_pasrep;
  public $droite_vocale;
  public $droite_tympan;

  // Form fields
  public $_gauche_aerien = array();
  public $_gauche_osseux = array();
  public $_gauche_conlat = array();
  public $_gauche_ipslat = array();
  public $_gauche_pasrep = array();
  public $_gauche_vocale = array();
  public $_gauche_tympan = array();

  public $_droite_aerien = array();
  public $_droite_osseux = array();
  public $_droite_conlat = array();
  public $_droite_ipslat = array();
  public $_droite_pasrep = array();
  public $_droite_vocale = array();
  public $_droite_tympan = array();

  public $_moyenne_gauche_aerien;
  public $_moyenne_gauche_osseux;
  public $_moyenne_droite_aerien;
  public $_moyenne_droite_osseux;

  // Fwd References
  /** @var CConsultation */
  public $_ref_consult;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'examaudio';
    $spec->key   = 'examaudio_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["consultation_id"] = "ref notNull class|CConsultation";
    $props["remarques"] = "text helped";
    $props["gauche_aerien"] = "str maxLength|64";
    $props["gauche_osseux"] = "str maxLength|64";
    $props["gauche_conlat"] = "str maxLength|64";
    $props["gauche_ipslat"] = "str maxLength|64";
    $props["gauche_pasrep"] = "str maxLength|64";
    $props["gauche_tympan"] = "str maxLength|64";
    $props["gauche_vocale"] = "str maxLength|64";
    $props["droite_aerien"] = "str maxLength|64";
    $props["droite_osseux"] = "str maxLength|64";
    $props["droite_conlat"] = "str maxLength|64";
    $props["droite_ipslat"] = "str maxLength|64";
    $props["droite_pasrep"] = "str maxLength|64";
    $props["droite_tympan"] = "str maxLength|64";
    $props["droite_vocale"] = "str maxLength|64";
    return $props;
  }

  /**
   * Vérifie que les abscisses des points vocaux sont conformes
   *
   * @param array $vocal_points Points vocaux
   *
   * @return bool
   */
  function checkAbscisse($vocal_points) {
    $dBs = array();
    foreach ($vocal_points as $point) {
      $point = explode("-", $point);
      $dB = $point[0];
      if (array_search($dB, $dBs) !== false) {
        return false;
      }

      if ($dB) {
        $dBs[] = $dB;
      }
    }

    return true;
  }

  /**
   * @see parent::check();
   */
  function check() {
    $msg = "Deux points ont la même abscisse dans l'audiogramme vocal de l'oreille ";
    if (!$this->checkAbscisse($this->_gauche_vocale)) {
      return $msg . "gauche";
    }

    if (!$this->checkAbscisse($this->_droite_vocale)) {
      return $msg . "droite";
    }

    return parent::check();
  }

  /**
   * @see parent::updateFormFields();
   */
  function updateFormFields() {
    parent::updateFormFields();

    // Initialisations
    $this->gauche_aerien = CValue::first($this->gauche_aerien, "|||||||");
    $this->gauche_osseux = CValue::first($this->gauche_osseux, "|||||||");
    $this->gauche_conlat = CValue::first($this->gauche_conlat, "|||||||");
    $this->gauche_ipslat = CValue::first($this->gauche_ipslat, "|||||||");
    $this->gauche_pasrep = CValue::first($this->gauche_pasrep, "|||||||");
    $this->gauche_tympan = CValue::first($this->gauche_tympan, "|||||||");
    $this->gauche_vocale = CValue::first($this->gauche_vocale, "|||||||");

    $this->droite_aerien = CValue::first($this->droite_aerien, "|||||||");
    $this->droite_osseux = CValue::first($this->droite_osseux, "|||||||");
    $this->droite_conlat = CValue::first($this->droite_conlat, "|||||||");
    $this->droite_ipslat = CValue::first($this->droite_ipslat, "|||||||");
    $this->droite_pasrep = CValue::first($this->droite_pasrep, "|||||||");
    $this->droite_tympan = CValue::first($this->droite_tympan, "|||||||");
    $this->droite_vocale = CValue::first($this->droite_vocale, "|||||||");

    $this->_gauche_aerien = explode("|", $this->gauche_aerien);
    $this->_gauche_osseux = explode("|", $this->gauche_osseux);
    $this->_gauche_conlat = explode("|", $this->gauche_conlat);
    $this->_gauche_ipslat = explode("|", $this->gauche_ipslat);
    $this->_gauche_pasrep = explode("|", $this->gauche_pasrep);
    $this->_gauche_vocale = explode("|", $this->gauche_vocale);
    $this->_gauche_tympan = explode("|", $this->gauche_tympan);

    $this->_droite_aerien = explode("|", $this->droite_aerien);
    $this->_droite_osseux = explode("|", $this->droite_osseux);
    $this->_droite_conlat = explode("|", $this->droite_conlat);
    $this->_droite_ipslat = explode("|", $this->droite_ipslat);
    $this->_droite_pasrep = explode("|", $this->droite_pasrep);
    $this->_droite_vocale = explode("|", $this->droite_vocale);
    $this->_droite_tympan = explode("|", $this->droite_tympan);

    $this->_moyenne_gauche_aerien =
      ($this->_gauche_aerien[2] + $this->_gauche_aerien[3] + $this->_gauche_aerien[4] + $this->_gauche_aerien[5]) / 4;
    $this->_moyenne_gauche_osseux =
      ($this->_gauche_osseux[2] + $this->_gauche_osseux[3] + $this->_gauche_osseux[4] + $this->_gauche_osseux[5]) / 4;
    $this->_moyenne_droite_aerien =
      ($this->_droite_aerien[2] + $this->_droite_aerien[3] + $this->_droite_aerien[4] + $this->_droite_aerien[5]) / 4;
    $this->_moyenne_droite_osseux =
      ($this->_droite_osseux[2] + $this->_droite_osseux[3] + $this->_droite_osseux[4] + $this->_droite_osseux[5]) / 4;

    foreach ($this->_gauche_vocale as $key => $value) {
      $item =& $this->_gauche_vocale[$key]; 
      $item = $value ? explode("-", $value) : array("", "");
    }

    foreach ($this->_droite_vocale as $key => $value) {
      $item =& $this->_droite_vocale[$key]; 
      $item = $value ? explode("-", $value) : array("", "");
    }
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    // Tris
    $dBs_gauche = array();
    foreach ($this->_gauche_vocale as $key => $value) {
      $dBs_gauche[] = CMbArray::get($value, 0, "end sort");
      $this->_gauche_vocale[$key] = CMbArray::get($value, 0) . "-" . CMbArray::get($value, 1);
    }

    array_multisort($dBs_gauche, SORT_ASC, $this->_gauche_vocale);

    $dBs_droite = array();
    foreach ($this->_droite_vocale as $key => $value) {
      $dBs_droite[] = CMbArray::get($value, 0, "end sort");
      $this->_droite_vocale[$key] = CMbArray::get($value, 0) . "-" . CMbArray::get($value, 1);
    }

    array_multisort($dBs_droite, SORT_ASC, $this->_droite_vocale);

    // Implodes
    $this->gauche_aerien = implode("|", $this->_gauche_aerien);
    $this->gauche_osseux = implode("|", $this->_gauche_osseux);
    $this->gauche_conlat = implode("|", $this->_gauche_conlat);
    $this->gauche_ipslat = implode("|", $this->_gauche_ipslat);
    $this->gauche_pasrep = implode("|", $this->_gauche_pasrep);
    $this->gauche_vocale = implode("|", $this->_gauche_vocale);
    $this->gauche_tympan = implode("|", $this->_gauche_tympan);

    $this->droite_aerien = implode("|", $this->_droite_aerien);
    $this->droite_osseux = implode("|", $this->_droite_osseux);
    $this->droite_conlat = implode("|", $this->_droite_conlat);
    $this->droite_ipslat = implode("|", $this->_droite_ipslat);
    $this->droite_pasrep = implode("|", $this->_droite_pasrep);
    $this->droite_vocale = implode("|", $this->_droite_vocale);
    $this->droite_tympan = implode("|", $this->_droite_tympan);
  }

  /**
   * Charge la consultation hôte
   *
   * @return CConsultation
   */
  function loadRefConsult() {
    return $this->_ref_consult = $this->loadFwdRef("consultation_id", true);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    return $this->loadRefConsult()->getPerm($permType);
  }
}
