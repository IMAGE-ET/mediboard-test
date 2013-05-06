<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Activite CsARR
 */
class CActiviteCsARR extends CCsARRObject {
  public $code;
  public $hierarchie;
  public $libelle;
  public $libelle_court;
  public $ordre;

  // Refs
  public $_ref_hierarchie;
  public $_ref_hierarchies;
  public $_ref_modulateurs;
  public $_ref_notes_activites;
  public $_ref_gestes_complementaires;
  public $_ref_activites_complementaires;

  // Counts
  public $_count_elements;
  public $_count_actes;
  public $_count_actes_by_executant;

  // Distant refs
  public $_ref_elements;
  public $_ref_elements_by_cat;
  public $_ref_all_executants;

  static $cached = array();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'activite';
    $spec->key   = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]          = "str notNull length|7 seekable show|0";
    $props["hierarchie"]    = "str notNull maxLength|12 seekable show|0";
    $props["libelle"]       = "str notNull seekable";
    $props["libelle_court"] = "str notNull seekable show|0";
    $props["ordre"]         = "num max|100";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code;
    $this->_shortview = $this->code;
  }

  function loadRefHierarchie() {
    return $this->_ref_hierarchie = CHierarchieCsARR::get($this->hierarchie);
  }

  function loadRefsHierarchies() {
    // Codes des hiérarchies intermédiaires
    $parts = explode(".", $this->hierarchie);
    $codes = array();
    foreach ($parts as $_part) {
      $codes[] = count($codes) ? end($codes) . ".$_part" : $_part;
    }

    // Chargement des hiérarchies intermédiaires
    $hierarchie = new CHierarchieCsARR;
    $hierarchies = $hierarchie->loadAll($codes);
    return $this->_ref_hierarchies = $hierarchies;
  }

  function loadRefsNotesActivites() {
    $note = new CNoteActiviteCsARR;
    $note->code = $this->code;
    $notes = array();
    foreach ($note->loadMatchingList("ordre") as $_note) {
      $notes[$_note->typenote][$_note->ordre] = $_note;
    }

    return $this->_ref_notes_activites = $notes;
  }

  function loadRefsModulateurs() {
    $modulateur = new CModulateurCsARR;
    $modulateur->code = $this->code;
    $modulateurs = $modulateur->loadMatchingList();
    return $this->_ref_modulateurs = $modulateurs;
  }

  function loadRefsGestesComplementaires() {
    // Chargement des gestes
    $geste = new CGesteComplementaireCsARR;
    $geste->code_source = $this->code;
    $gestes = $geste->loadMatchingList();
    $this->_ref_gestes_complementaires = $gestes;

    // Chargement directes des activités correspondantes.
    $codes = CMbArray::pluck($gestes, "code_cible");
    $activite = new CActiviteCsARR;
    $this->_ref_activites_complementaires = $activite->loadAll($codes);

    // Retour de gestes
    return $this->_ref_gestes_complementaires;

  }

  function loadView(){
    parent::loadView();
    $this->loadRefHierarchie();
  }

  function countElements() {
    $element = new CElementPrescriptionToCsarr();
    $element->code = $this->code;
    return $this->_count_elements = $element->countMatchingList();
  }

  function loadRefsElements() {
    $element = new CElementPrescriptionToCsarr();
    $element->code = $this->code;
    return $this->_ref_elements = $element->loadMatchingList();
  }

  function loadRefsElementsByCat() {
    $this->_ref_elements_by_cat = array();
    foreach ($this->loadRefsElements() as $_element) {
      if ($element = $_element->loadRefElementPrescription()) {
        $this->_ref_elements_by_cat[$element->category_prescription_id][] = $_element;
      }
    }
    return $this->_ref_elements_by_cat;
  }

  function countActes() {
    $acte = new CActeCdARR();
    $acte->code = $this->code;
    return $this->_count_actes = $acte->countMatchingList();
  }

  function loadRefsAllExecutants() {
    // Comptage par executant
    $query = "SELECT therapeute_id, COUNT(*)
      FROM `acte_csarr` 
      LEFT JOIN `evenement_ssr` ON  `evenement_ssr`.`evenement_ssr_id` = `acte_csarr`.`evenement_ssr_id`
      WHERE `code` = '$this->code'
      GROUP BY `therapeute_id`";
    $acte = new CActeCsARR();  
    $ds = $acte->getDS();
    $counts = $ds->loadHashList($query);
    arsort($counts);

    // Chargement des executants
    $user = new CMediusers;
    $executants = $user->loadAll(array_keys($counts));
    foreach ($executants as $_executant) {
      $_executant->loadRefFunction();
    }

    // Valeurs de retour
    $this->_count_actes_by_executant = $counts;
    return $this->_ref_all_executants = $executants;
  }


  /**
   * Get an instance from the code
   * @param $code string
   * @return CActiviteCdARR
   **/
  static function get($code) {
    if (!$code) {
      return new self();
    }

    if (!isset(self::$cached[$code])) {
      $activite = new self();
      $activite->load($code);
      $activite->loadRefsModulateurs();
      self::$cached[$code] = $activite;
    }

    return self::$cached[$code];
  }
}
