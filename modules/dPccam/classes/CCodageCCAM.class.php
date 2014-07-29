<?php

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

/**
 * Link the association rule used by the practitioner to the CCodable
 */
class CCodageCCAM extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $codage_ccam_id;

  public $association_rule;
  public $association_mode;

  public $codable_class;
  public $codable_id;
  public $praticien_id;
  public $locked;
  public $nb_acts;

  /**
   * @var CActeCCAM[]
   */
  protected $_ordered_acts;

  /**
   * @var boolean[]
   */
  public $_possible_rules;

  /**
   * @var array
   */
  protected $_check_rules;

  protected $_check_asso  = true;
  protected $_apply_rules = true;

  /**
   * @var CCodable
   */
  public $_ref_codable;

  /**
   * @var CMediusers
   */
  public $_ref_praticien;

  /**
   * @var CActeCCAM[]
   */
  public $_ref_actes_ccam;
  /**
   * @var CActeCCAM[]
   */
  public $_ref_actes_ccam_facturables;

  protected static $association_rules = array(
    'G1'  => 'auto',
    'EA'  => 'ask',
    'EB'  => 'ask',
    'EC'  => 'ask',
    'ED'  => 'ask',
    'EE'  => 'ask',
    'EF'  => 'ask',
    'EG1' => 'auto',
    'EG2' => 'auto',
    'EG3' => 'auto',
    'EG4' => 'auto',
    'EG5' => 'auto',
    'EG6' => 'auto',
    'EG7' => 'auto',
    'EH'  => 'auto',
    'EI'  => 'auto',
    'GA'  => 'auto',
    'GB'  => 'auto',
    'G2'  => 'auto'
  );

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();

    $spec->table   = 'codage_ccam';
    $spec->key     = 'codage_ccam_id';
    $spec->uniques['codable_praticien'] = array('codable_class', 'codable_id', 'praticien_id');

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  public function getProps() {
    $props = parent::getProps();

    $props['association_rule'] = 'enum list|G1|EA|EB|EC|ED|EE|EF|EG1|EG2|EG3|EG4|EG5|EG6|EG7|EH|EI|GA|GB|G2|M';
    $props['association_mode'] = 'enum list|auto|user_choice default|auto';
    $props['codable_class'] = 'str notNull class';
    $props['codable_id'] = 'ref notNull class|CCodable meta|codable_class';
    $props['praticien_id'] = 'ref notNull class|CMediusers';
    $props['locked'] = 'bool notNull default|0';
    $props['nb_acts'] = 'num notNull default|0';

    return $props;
  }

  /**
   * Return the CCodageCCAM linked to the given codable and practitioner, and create it if it not exists
   *
   * @param CCodable $codable      The codable object
   * @param integer  $praticien_id The practitioner id
   *
   * @return CCodageCCAM
   */
  public static function get($codable, $praticien_id) {
    $codage_ccam = new CCodageCCAM();
    $codage_ccam->codable_class = $codable->_class;
    $codage_ccam->codable_id = $codable->_id;
    $codage_ccam->praticien_id = $praticien_id;
    $codage_ccam->loadMatchingObject();

    if (!$codage_ccam->_id) {
      $codage_ccam->store();
    }

    return $codage_ccam;
  }

  /**
   * Load the codable object
   *
   * @param bool $cache Use object cache
   *
   * @return CCodable|null
   */
  public function loadCodable($cache = true) {
    if (!$this->codable_class || !$this->codable_id) {
      return null;
    }

    return $this->_ref_codable = $this->loadFwdRef('codable_id', $cache);
  }

  /**
   * Load the practitioner
   *
   * @param bool $cache Use object cache
   *
   * @return CMediusers|null
   */
  public function loadPraticien($cache = true) {
    return $this->_ref_praticien = $this->loadFwdRef('praticien_id', $cache);
  }

  /**
   * @see parent::getPerm()
   */
  public function getPerm($permType) {
    $this->loadPraticien();
    return $this->_ref_praticien->getPerm($permType);
  }

  /**
   * Load the linked acts of the given act
   *
   * @return CActeCCAM[]
   */
  public function loadActesCCAM() {
    if ($this->_ref_actes_ccam) {
      return $this->_ref_actes_ccam;
    }

    $act = new CActeCCAM();
    $act->object_class = $this->codable_class;
    $act->object_id = $this->codable_id;
    $act->executant_id = $this->praticien_id;
    $this->_ref_actes_ccam = $act->loadMatchingList("code_association");

    if ($this->nb_acts != count($this->_ref_actes_ccam)) {
      $this->nb_acts = count($this->_ref_actes_ccam);
    }

    foreach ($this->_ref_actes_ccam as $_acte) {
      $_acte->loadRefCodeCCAM();
    }

    return $this->_ref_actes_ccam;
  }

  /**
   * Force the update of the rule
   *
   * @return bool
   */
  function updateRule() {
    if ($this->association_mode != 'auto' || !$this->_check_asso) {
      return false;
    }
    $this->guessRule();
    if ($this->fieldModified('association_rule')) {
      $this->applyRuleToActes();
      return true;
    }
    $this->_check_asso = false;
    return false;
  }

  /**
   * @see parent::check()
   */
  public function check() {
    $this->completeField('codable_class', 'codable_id', 'praticien_id', 'association_mode', 'association_rule', 'locked');

    if ($this->_old->locked) {
      return "";
    }
    if (!$this->_id || $this->fieldModified('association_mode', 'auto')) {
      $this->guessRule();
    }
    if (!$this->_id || $this->fieldModified('association_rule')) {
      $this->applyRuleToActes();
    }
    return parent::check();
  }

  /**
   * Guess the correct rule and replace it
   *
   * @return string
   */
  function guessRule() {
    if ($this->association_mode != 'auto') {
      return "";
    }
    return $this->association_rule = $this->checkRules();
  }

  /**
   * Guess the association code of all actes
   *
   * @return void
   */
  function guessActesAssociation() {
    $this->completeField("association_rule");
    $this->getActsByTarif();
    foreach ($this->_ref_actes_ccam as $_act) {
      $_act->_position = array_search($_act->_id, array_keys($this->_ordered_acts));
      $this->guessActeAssociation($this->association_rule, $_act);
    }
  }

  /**
   * Apply the rule to all actes
   *
   * @return void
   */
  function applyRuleToActes() {
    if (!$this->_apply_rules) {
      return;
    }
    $this->completeField("association_rule");
    $this->getActsByTarif();
    foreach ($this->_ref_actes_ccam as $_act) {
      $_act->_position = array_search($_act->_id, array_keys($this->_ordered_acts));
      $this->applyRule($this->association_rule, $_act);
      $_act->store();
    }
    $this->_apply_rules = false;
  }

  /**
   * Order the acts by price
   *
   * @return array
   */
  protected function getActsByTarif() {
    $this->loadActesCCAM();
    $this->checkFacturableActs();
    if (!isset($this->_ordered_acts)) {
      $this->_ordered_acts = array();
    }
    if (count($this->_ref_actes_ccam_facturables) == count($this->_ordered_acts)) {
      return $this->_ordered_acts;
    }

    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      $this->_ordered_acts[$_act->_id] = $_act->getTarifSansAssociationNiCharge();
    }
    return $this->_ordered_acts = self::orderActsByTarif($this->_ordered_acts);
  }

  /**
   * Reorder the acts by price
   *
   * @param array $disordered_acts The acts to reorder
   *
   * @return array
   */
  protected static function orderActsByTarif($disordered_acts) {
    ksort($disordered_acts);
    arsort($disordered_acts);

    return $disordered_acts;
  }

  /**
   * Reset the facturable field of the acts, and make the acts with a price equal to 0 unfacturable
   *
   * @return void
   */
  protected function checkFacturableActs() {
    $this->_ref_actes_ccam_facturables = array();
    foreach ($this->_ref_actes_ccam as $_acte) {
      if (!$_acte->facturable) {
        $_acte->_guess_facturable = '1';
      }
      if ($_acte->getTarifSansAssociationNiCharge() == 0) {
        $_acte->_guess_facturable = '0';
      }
      else {
        $this->_ref_actes_ccam_facturables[$_acte->_id] = $_acte;
      }
    }
  }

  /**
    * Check the modifiers of the given act
    *
    * @param CObject  $modifiers The modifiers to check
    * @param string   $execution The dateTime of the execution of the act
    * @param CCodable $codable   The codable
    *
    * @return void
    */
  public static function checkModifiers($modifiers, $execution, $codable) {
    $date = CMbDT::date(null, $execution);
    $time = CMbDT::time(null, $execution);
    $discipline = $codable->_ref_praticien->_ref_discipline;
    $patient = $codable->_ref_patient;

    foreach ($modifiers as $_modifier) {
      switch ($_modifier->code) {
        case 'A':
          $_modifier->_checked = ($patient->_annees < 4 || $patient->_annees > 80);
          break;
        case 'E':
          $_modifier->_checked = $patient->_annees < 5;
          break;
        case 'F':
          $_modifier->_checked = (CMbDT::transform('', $execution, '%w') == 0 || CMbDate::isHoliday(CMbDT::date(null, $execution)));
          break;
        case 'N':
          $_modifier->_checked = $patient->_annees < 13;
          break;
        case 'P':
          // gerer specialite cpam?
          $_modifier->_checked = (in_array($discipline->text, array("MEDECINE GENERALE", "PEDIATRIE")) &&
            ($time >= "20:00:00" && $time < "00:00:00"));
          break;
        case 'S':
          // Gérer : 'ou autres med. pr acte thérapeutique sous anesthésie
          $_modifier->_checked = (in_array($discipline->text, array("MEDECINE GENERALE", "PEDIATRIE")) ||
              ($codable->_class == "COperation" && $codable->_lu_type_anesth)) &&
            ($time >= "00:00:00" && $time <= "08:00:00");
          break;
        case 'U':
          $_modifier->_checked = !in_array($discipline->text, array("MEDECINE GENERALE", "PEDIATRIE")) &&
            ($time >= '20:00:00' || $time <= '08:00:00');
          break;
        case "7":
          $_modifier->_checked = CAppUI::conf("dPccam CCodable precode_modificateur_7");
          break;
        case "J":
          $_modifier->_checked = $codable->_class == 'COperations' && CAppUI::conf("dPccam CCodable precode_modificateur_J");
          break;
        default:
          $_modifier->_checked = 0;
          break;
      }
    }
  }

  /**
   * Vérification de l'application d'une règle nommée sur un acte
   *
   * @param string    $rulename Rule name
   * @param CActeCCAM &$act     The act
   *
   * @return void
   */
  public function guessActeAssociation($rulename, &$act) {
    if ($act->_position === false) {
      $act->facturable = 0;
      $act->_guess_association = '';
      $act->_guess_regle_asso = $rulename;
    }
    else {
      $act->loadRefCodeCCAM();
      call_user_func(array($this, "applyRule$rulename"), $act);
    }
  }

  /**
   * Application d'une règle nommée sur un acte
   *
   * @param string    $rulename Rule name
   * @param CActeCCAM &$act     The act
   *
   * @return void
   */
  protected function applyRule($rulename, &$act) {
    $this->guessActeAssociation($rulename, $act);
    $act->code_association = $act->_guess_association;
    $act->facturable       = $act->_guess_facturable;
  }

  /**
   * Guess the association code for an act
   *
   * @return string
   */
  public function checkRules() {
    $this->getActsByTarif();
    $this->_check_rules = array();
    $this->_possible_rules = array();
    $firstRule = null;

    foreach (self::$association_rules as $_rule => $_type) {
      if (self::isRuleAllowed($_rule)) {
        $this->_possible_rules[$_rule] = call_user_func(array($this, "checkRule$_rule"));
        if ($firstRule === null && $this->_possible_rules[$_rule] && $_type == "auto") {
          $firstRule = $_rule;
        }
      }
    }
    return $firstRule;
  }

  /**
   * Check if the rule is allowed to be used
   *
   * @param string $rule The name of the rule
   *
   * @return boolean
   */
  protected static function isRuleAllowed($rule) {
    $feature = "dPccam associations rules $rule";
    if (strpos($rule, 'G') === 0) {
      $feature = "dPccam associations rules G";
    }

    return CAppUI::conf($feature, CGroups::loadCurrent()->_guid);
  }

  /** Association rules **/

  /**
   * Check the association rule G1
   *
   * @return bool
   */
  protected function checkRuleG1() {
    if (count($this->_ref_actes_ccam_facturables) != 1) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule G1 to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleG1(&$act) {
    switch ($act->_position) {
      case 0:
        $act->_guess_facturable = '1';
        $act->_guess_association = '';
        $act->_guess_regle_asso = 'G1';
        break;
      default:
        $act->_guess_facturable = '0';
        $act->_guess_association = '';
        $act->_guess_regle_asso = 'G1';
    }
  }

  /**
   * ### Règle d'association générale A ###
   *
   * * Nombre d'actes : 2
   * * Cas d'utilisation : Dans le cas d'une association de __2 actes seulement__, dont l'un est un soit un geste
   * complémentaire, soit un supplément, soit un acte d'imagerie pour acte de radiologie interventionnelle ou cardiologie
   * interventionnelle (Paragraphe 19.01.09.02), il ne faut pas indiquer de code d'association
   *
   * @return bool
   */
  protected function checkRuleGA() {
    if (count($this->_ref_actes_ccam_facturables) != 2) {
      return false;
    }

    $complement = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {

      if (
          $_acte_ccam->_ref_code_ccam->isComplement() ||
          $_acte_ccam->_ref_code_ccam->isSupplement() ||
          $_acte_ccam->_ref_code_ccam->isRadioCardioInterv()
      ) {
        $complement++;
      }
    }

    if ($complement != 1) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule GA to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleGA(&$act) {
    if (
        $act->_position == 0 ||
        $act->_ref_code_ccam->isSupplement() ||
        $act->_ref_code_ccam->isComplement()
    ) {

      $act->_guess_facturable = '1';
      $act->_guess_association = '';
      $act->_guess_regle_asso = 'GA';
    }
    else {
      $act->_guess_facturable = '0';
      $act->_guess_association = '';
      $act->_guess_regle_asso = 'GA';

    }
  }

  /**
   * ### Règle d'association générale B ###
   * * Nombre d'actes : 3
   * * Cas d'utilisation : Si un acte est associé à un geste complémentaire et à un supplément, le code d'assciation est 1 pour
   * chacun des actes.
   *
   * @return bool
   */
  protected function checkRuleGB() {
    if (count($this->_ref_actes_ccam_facturables) != 3) {
      return false;
    }

    $supp = 0;
    $comp = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {
      if ($_acte_ccam->_ref_code_ccam->isComplement()) {
        $comp++;
      }
      if ($_acte_ccam->_ref_code_ccam->isSupplement()) {
        $supp++;
      }
    }

    if ($supp != 1 || $comp != 1) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule GB to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleGB(&$act) {
    if (
        $act->_position == 0 ||
        $act->_ref_code_ccam->isSupplement() ||
        $act->_ref_code_ccam->isComplement()
    ) {
      $act->_guess_facturable = '1';
      $act->_guess_association = '1';
      $act->_guess_regle_asso = 'GB';
    }
    else {
      $act->_guess_facturable = '0';
      $act->_guess_association = '';
      $act->_guess_regle_asso = 'GB';
    }
  }

  /**
   * Check the association rule G2
   *
   * @return bool
   */
  protected function checkRuleG2() {
    return true;
  }

  /**
   * Apply the association rule G2 to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleG2(&$act) {
    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {
      if (
          $_acte_ccam->_ref_code_ccam->isSupplement() ||
          $_acte_ccam->_ref_code_ccam->isComplement()
      ) {
        unset($this->_ordered_acts[$_acte_ccam->_id]);
        if ($_acte_ccam->_id == $act->_id) {
          $act->_position = -1;
        }
      }
    }

    if ($act->_position != -1) {
      self::orderActsByTarif($this->_ordered_acts);
      $act->_position = array_search($act->_id, array_keys($this->_ordered_acts));
    }

    switch ($act->_position) {
      case -1:
        $act->_guess_facturable = '1';
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'G2';
        break;
      case 0:
        $act->_guess_facturable = '1';
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'G2';
        break;
      case 1:
        $act->_guess_facturable = '1';
        $act->_guess_association = '2';
        $act->_guess_regle_asso = 'G2';
        break;
      default:
        $act->_guess_facturable = '0';
        $act->facturable = '0';
        $act->_guess_association = '0';
    }
  }

  /**
   * ### Exception sur les actes de chirugie (membres différents) ###
   * * Nombre d'actes : 2
   * * Cas d'utilisation : Pour les __actes de chirurgie portant sur des membres différents__ (sur le tronc et un membre,
   * sur la tête et un membre), l'acte dont le tarif (hors modificateurs) est le moins élevé est tarifé à 75% de sa valeur
   *
   * @return bool
   */
  protected function checkRuleEA() {
    $chap11 = 0;
    $chap12 = 0;
    $chap13 = 0;
    $chap14 = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      switch ($_act->_ref_code_ccam->chapitres[0]['db']) {
        case '000011':
          $chap11++;
          break;
        case '000012':
          $chap12++;
          break;
        case '000013':
          $chap13++;
          break;
        case '000014':
          $chap14++;
          break;
        default:
      }
    }

    if (count($this->_ref_actes_ccam_facturables) < 2 || (!$chap11 && !$chap12 && !$chap13 && !$chap14)) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule EA to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEA(&$act) {
    $ordered_acts_ea = $this->_ordered_acts;
    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      $chap = $_act->_ref_code_ccam->chapitres;
      if ($_act->_ref_code_ccam->isSupplement()) {
        unset($ordered_acts_ea[$_act->_id]);
        if ($_act->_id == $act->_id) {
          $act->_position = -1;
        }
      }
    }
    if ($act->_position != -1) {
      $ordered_acts_ea = self::orderActsByTarif($ordered_acts_ea);
      $act->_position = array_search($act->_id, array_keys($ordered_acts_ea));
    }

    switch ($act->_position) {
      case -1:
      case 0:
        $act->_guess_facturable = '1';
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'EA';
        break;
      case 1:
        $act->_guess_facturable = '1';
        $act->_guess_association = '3';
        $act->_guess_regle_asso = 'EA';
        break;
      case 2:
      case 3:
        $act->_guess_facturable = '1';
        $act->_guess_association = '2';
        $act->_guess_regle_asso = 'EA';
        break;
      default:
        $act->_guess_facturable = '0';
        $act->_guess_association = '';
        $act->_guess_regle_asso = 'EA';
    }
  }

  /**
   * ### Exception sur les actes de chirugie (lésions traumatiques multiples et récentes) ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Pour les __actes de chirurgie pour lésions traumatiques et récentes__, l'association de
   * trois actes au plus, y comprit les gestes complémentaires, peut être tarifée.
   * L'acte dont le tarif (hors modificateurs) est le plus élevé est tarifé à taux plein. Le deuxième est tarifé à
   * 75% de sa valeur, et le troisième à 50%.
   *
   * @return bool
   */
  protected function checkRuleEB() {
    $nb_chir = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      $classif = reset($_act->_ref_code_ccam->_ref_code_ccam->_ref_activites[$_act->code_activite]->_ref_classif);
      if ($classif->code_regroupement == 'ADC') {
        $nb_chir++;
      }
    }
    if (!$nb_chir) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule EB to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEB(&$act) {
    switch ($act->_position) {
      case 0:
        $act->_guess_facturable = '1';
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'EB';
        break;
      case 1:
        $act->_guess_facturable = '1';
        $act->_guess_association = '3';
        $act->_guess_regle_asso = 'EB';
        break;
      case 2:
        $act->_guess_facturable = '1';
        $act->_guess_association = '2';
        $act->_guess_regle_asso = 'EB';
        break;
      default:
        $act->_guess_facturable = '0';
        $act->_guess_association = '';
        $act->_guess_regle_asso = 'EB';
    }
  }

  /**
   * ### Actes de chirugie carcinologique en ORL associant une exérèse, un curage et une reconstruction ###
   * * Nombre d'actes : 3
   * * Cas d'utilisation : Pour les __actes de chirugie carcinologique en ORL associant une exérèse, un curage et une reconstruction__,
   * l'acte dont le tarif (hots modificateurs) est le plus élevé est tarifé à taux plein, le deuxième et le troisième sont tarifés
   * à 50% de leurs valeurs.
   *
   * @return bool
   */
  protected function checkRuleEC() {
    $exerese = false;
    $curage = false;
    $reconst = false;
    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {
      $libelle = $_acte_ccam->_ref_code_ccam->libelleLong;
      if (stripos($libelle, 'exérèse') !== false) {
        $exerese = true;
      }
      elseif (stripos($libelle, 'curage') !== false) {
        $curage = true;
      }
      elseif (stripos($libelle, 'reconstruction') !== false) {
        $reconst = true;
      }
    }

    if (!$exerese && !$curage && !$reconst) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule EC to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEC(&$act) {
    switch ($act->_position) {
      case 0:
        $act->_guess_facturable = '1';
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'EC';
        break;
      case 1:
        $act->_guess_facturable = '1';
        $act->_guess_association = '2';
        $act->_guess_regle_asso = 'EC';
        break;
      case 2:
        $act->_guess_facturable = '1';
        $act->_guess_association = '2';
        $act->_guess_regle_asso = 'EC';
        break;
      default:
        $act->_guess_facturable = '0';
        $act->_guess_association = '';
        $act->_guess_regle_asso = 'EC';
    }
  }

  /**
   * Actes d'échographie portant sur plusieurs régions anatomiques
   *
   * @return bool
   */
  protected function checkRuleED() {
    $chapters_echo = array(
      '01.01.03.',
      '02.01.02.',
      '04.01.03.',
      '06.01.02.',
      '07.01.03.',
      '08.01.02.',
      '09.01.02.',
      '10.01.01.',
      '14.01.01.',
      '15.01.01.',
      '16.01.01.',
      '16.02.01.',
      '17.01.01.',
      '19.01.04.',
    );
    $nb_echo = 0;

    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;
      if (in_array($chapters[2]['rang'], $chapters_echo)) {
        $nb_echo++;
      }
    }

    if (!$nb_echo) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule ED to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleED(&$act) {
    switch ($act->_position) {
      case 0:
        $act->_guess_facturable = '1';
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'ED';
        break;
      case 1:
        $act->_guess_facturable = '1';
        $act->_guess_association = '2';
        $act->_guess_regle_asso = 'ED';
        break;
      default:
        $act->_guess_facturable = '0';
        $act->_guess_association = '';
        $act->_guess_regle_asso = 'ED';
    }
  }

  /**
   * ### Actes de scanographie ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Pour les __actes de scanographie, lorsque l'examen porte sur plusieurs régions anatomiques__,
   * un seul acte doit être tarifé, sauf dans le cas ou l'examen effectué est conjoint des régions anatomiques suivantes :
   * membres et tête, membres et thorax, membres et abdomen, tête et abdomen, thorax et abdomen complet, tête et thorax,
   * quel que soit le nombres de coupes nécéssaires, avec ou sans injection de produit de contraste.
   *
   * Dans ce cas, deux actes ou plus peuvent être tarifés à taux plein. Deux forfaits techniques peuvent alors être facturés,
   * le second avec une minaration de 85% de son tarfi.
   *
   * Quand un libellé décrit l'examen conjoint de plusieurs régions anatomiques, il ne peut être tarifé avec aucun autre acte
   * de scanographie. Deux forfaits techniques peuvent alors être tarifés, le second avec une minoration de 85% de son tarfi.
   *
   * L'acte de guidage scanographique ne peut être tarfié qu'avec les actes dont le libellé précise qu'ils nécessitent un
   * guidage scanoraphique. Dans ce cas, deux acte au plus peuvent être tarifés à taux plein.
   *
   * @return bool
   */
  protected function checkRuleEE() {
    $chapters_scano = array(
      '01.01.05.',
      '04.01.05.',
      '05.01.02.',
      '06.01.04.',
      '07.01.05.',
      '09.01.04.',
      '11.01.04.',
      '12.01.04.',
      '13.01.02',
      '14.01.03.',
      '16.01.02.',
      '16.02.03.',
      '17.01.03.',
    );
    $nb_scano = 0;

    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;
      if (in_array($chapters[2]['rang'], $chapters_scano)) {
        $nb_scano++;
      }
    }

    if (!$nb_scano) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule EE to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEE(&$act) {
    switch ($act->_position) {
      case 0:
        $act->_guess_facturable = '1';
        $act->_guess_association = '4';
        $act->_guess_regle_asso = 'EE';
        break;
      case 1:
        $act->_guess_facturable = '1';
        $act->_guess_association = '4';
        $act->_guess_regle_asso = 'EE';
        break;
      case 2:
        $act->_guess_facturable = '1';
        $act->_guess_association = '4';
        $act->_guess_regle_asso = 'EE';
        break;
      default:
        $act->_guess_facturable = '0';
        $act->_guess_association = '';
        $act->_guess_regle_asso = 'EE';
    }
  }

  /**
   * Association rule EF
   *
   * @return bool
   */
  protected function checkRuleEF() {
    $chapters_remno = array(
      '01.01.06.',
      '04.01.06.',
      '05.01.03.',
      '06.01.05.',
      '07.01.06.',
      '11.01.05.',
      '12.01.05.',
      '13.01.03.',
      '14.01.04.',
      '16.01.03.',
      '16.02.04.',
      '17.01.04.',
    );
    $nb_remno = 0;
    $guidage_remno = 0;

    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;
      if (strpos($_acte_ccam->_ref_code_ccam->libelleLong, 'guidage remnographique') !== false) {
        $guidage_remno++;
      }
      elseif (in_array($chapters[2]['rang'], $chapters_remno)) {
        $nb_remno++;
      }
    }

    if (!$nb_remno && !$guidage_remno) {
      return false;
    }

    $this->_check_rules['EF'] = array(
      'nb_remno' => $nb_remno,
      'guidage_remno' => $guidage_remno,
    );

    return true;
  }

  /**
   * Apply the association rule EF to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEF(&$act) {
    if ($this->_check_rules['EF']['guidage_remno'] == 2) {
      switch ($act->_position) {
        case 0:
          $act->_guess_facturable = '1';
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EF';
          break;
        case 1:
          $act->_guess_facturable = '1';
          $act->_guess_association = '2';
          $act->_guess_regle_asso = 'EF';
          break;
        default:
          $act->_guess_facturable = '0';
          $act->_guess_association = '';
          $act->_guess_regle_asso = 'EF';
      }
    }
    else {
      switch ($act->_position) {
        case 0:
          $act->_guess_facturable = '1';
          $act->_guess_association = '';
          $act->_guess_regle_asso = 'EF';
          break;
        default:
          $act->_guess_facturable = '0';
          $act->_guess_association = '';
          $act->_guess_regle_asso = 'EF';
      }
    }
  }

  /**
   * ### Eception actes de radiologie vasculaire et imagerie conventionnelle ###
   * * Nombre d'actes : 2
   * * Cas d'utilisation : Les __actes du sous paragraphe 19.01.09.02__ (radiologie vasculaire et imagerie conventionnelle)
   * sont associés à taux plein, deux actes au plus peuvent tarifés.
   *
   * @return bool
   */
  protected function checkRuleEG1() {
    $cond = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;
      if (
          $chapters[0]['db'] == '000019' && $chapters[1]['db'] == '000001' &&
          $chapters[2]['db'] == '000009' && $chapters[3]['db'] == '000002'
      ) {
        $cond++;
      }
    }

    if ($cond != 2) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule EG1 to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEG1(&$act) {
    switch ($act->_position) {
      case 0:
        $act->_guess_facturable = '1';
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'EG1';
        break;
      case 1:
        $act->_guess_facturable = '1';
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'EG1';
        break;
      default:
        $act->_guess_facturable = '0';
        $act->_guess_association = '';
        $act->_guess_regle_asso = 'EG1';
    }
  }

  /**
   * ### Exception : actes d'anatomie et de cytologie pathologique ###
   * * Nombre d'actes : 2 ou3
   * * Cas d'utilisation : Les __actes d'anatomie et de cytologie pathologique__ peuvent être associés à
   * taux plein entre eux et/ou à un autre acte, quelque soit le nombre d'acte d'anatomie et de cytologie pathologique.
   *
   * @return bool
   */
  protected function checkRuleEG2() {
    $ordered_acts_eg2 = $this->_ordered_acts;
    $chapters_anapath = array(
      '01.01.14.',
      '02.01.10.',
      '04.01.10.',
      '05.01.08.',
      '06.01.11.',
      '07.01.13.',
      '08.01.09.',
      '09.01.07.',
      '10.01.05.',
      '15.01.07.',
      '16.01.06.',
      '16.02.06.',
      '17.02.'
    );
    $nb_anapath = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      $chap = $_act->_ref_code_ccam->chapitres;
      if (in_array($chap[2]['rang'], $chapters_anapath) || in_array($chap[1]['rang'], $chapters_anapath)) {
        $nb_anapath++;
      }
    }

    if (!$nb_anapath) {
      return false;
    }

    $this->_check_rules['EG2'] = array(
      'nb_anapath' => $nb_anapath,
    );

    return true;
  }

  /**
   * Apply the association rule EG2 to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEG2(&$act) {
    $ordered_acts_eg2 = $this->_ordered_acts;
    $chapters_anapath = array(
      '01.01.14.',
      '02.01.10.',
      '04.01.10.',
      '05.01.08.',
      '06.01.11.',
      '07.01.13.',
      '08.01.09.',
      '09.01.07.',
      '10.01.05.',
      '15.01.07.',
      '16.01.06.',
      '16.02.06.',
      '17.02.'
    );

    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      $chap = $_act->_ref_code_ccam->chapitres;
      if (in_array($chap[2]['rang'], $chapters_anapath) || in_array($chap[1]['rang'], $chapters_anapath)) {
        unset($ordered_acts_eg2[$_act->_id]);
        if ($_act->_id == $act->_id) {
          $act->_position = -1;
        }
      }
    }
    if ($act->_position != -1) {
      $ordered_acts_eg2 = self::orderActsByTarif($ordered_acts_eg2);
      $act->_position = array_search($act->_id, array_keys($ordered_acts_eg2));
    }

    $nb_anapath = $this->_check_rules['EG2']['nb_anapath'];
    if ($nb_anapath == 2 || ($nb_anapath == 1 && count($ordered_acts_eg2) == 1)) {
      $act->_guess_facturable = '1';
      $act->_guess_association = '4';
      $act->_guess_regle_asso = 'EG2';
    }
    else {
      switch ($act->_position) {
        case -1:
          $act->_guess_facturable = '1';
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EG2';
          break;
        case 0:
          $act->_guess_facturable = '1';
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EG2';
          break;
        case 1:
          $act->_guess_facturable = '1';
          $act->_guess_association = '2';
          $act->_guess_regle_asso = 'EG2';
          break;
        default:
          $act->_guess_facturable = '0';
          $act->_guess_association = '';
          $act->_guess_regle_asso = 'EG2';
      }
    }
  }

  /**
   * ### Exception : actes d'électromyographie, de mesure de vitesse de conduction, d'études des lances et des réflexes ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Les __actes d'électromyographie, de mesure de vitesse de conduction, d'études des lances et des réflexes__
   * (figurants aux paragraphes 01.01.01.01, 01.01.01.02, 01.01.01.03 de la CCAM) peuvent être associés à taux plein entre eux ou à
   * un autre acte, quelque soit le nombre d'actes
   *
   * @return bool
   */
  protected function checkRuleEG3() {
    $nb_electromyo = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;
      if (
          $chapters[0]['db'] == '000001' && $chapters[1]['db'] == '000001' && $chapters[2]['db'] == '000001' &&
          ($chapters[3]['db'] == '000001' || $chapters[3]['db'] == '000002' || $chapters[3]['db'] == '000003' )
      ) {
        $nb_electromyo++;
      }
    }

    if (!$nb_electromyo) {
      return false;
    }

    $this->_check_rules['EG3'] = array(
      'nb_electromyo'
    );

    return true;
  }

  /**
   * Apply the association rule EG3 to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEG3(&$act) {
    $ordered_acts_eg3 = $this->_ordered_acts;
    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;
      if (
          $chapters[0]['db'] == '000001' && $chapters[1]['db'] == '000001' && $chapters[2]['db'] == '000001' &&
          ($chapters[3]['db'] == '000001' || $chapters[3]['db'] == '000002' || $chapters[3]['db'] == '000003' )
      ) {
        unset($ordered_acts_eg3[$_acte_ccam->_id]);
        if ($_acte_ccam->_id == $act->_id) {
          $act->_position = -1;
        }
      }
      elseif ($chapters[0]['db'] == '000019' && $chapters[1]['db'] == '000002') {
        unset($ordered_acts_eg3[$_acte_ccam->_id]);
        if ($_acte_ccam->_id == $act->_id) {
          $act->_position = -1;
        }
      }
    }
    if ($act->_position != -1) {
      $ordered_acts_eg3 = self::orderActsByTarif($ordered_acts_eg3);
      $act->_position = array_search($act->_id, array_keys($ordered_acts_eg3));
    }

    $nb_electromyo = $this->_check_rules['EG3']['nb_electromyo'];

    if ($nb_electromyo == 2 || ($nb_electromyo == 1 && count($ordered_acts_eg3) == 1)) {
      $act->_guess_facturable = '1';
      $act->_guess_association = '4';
      $act->_guess_regle_asso = 'EG3';
    }
    else {
      switch ($act->_position) {
        case -1:
          $act->_guess_facturable = '1';
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EG3';
          break;
        case 0:
          $act->_guess_facturable = '1';
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EG3';
          break;
        case 1:
          $act->_guess_facturable = '1';
          $act->_guess_association = '2';
          $act->_guess_regle_asso = 'EG3';
          break;
        default:
          $act->_guess_facturable = '0';
          $act->_guess_association = '';
          $act->_guess_regle_asso = 'EG3';
      }
    }
  }

  /**
   * ### Exception : actes d'irradiation en radiothérapie ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Les __actes d'irradiation en radiothérapie__, ainsi que les suppléments autorisés avec ces actes,
   * peuvent être associés à taux plein, quel que soit le nombre d'actes.
   *
   * @return bool
   */
  protected function checkRuleEG4() {
    $irrad = 0;
    $supp = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;
      if (
          ($chapters[0]['db'] == '000017' && $chapters[1]['db'] == '000004' && $chapters[2]['db'] == '000002') ||
          ($chapters[0]['db'] == '000019' && $chapters[1]['db'] == '000001' && $chapters[2]['db'] == '000010')
      ) {
        $irrad++;
      }
      elseif (
          $_acte_ccam->_ref_code_ccam->isSupplement() ||
          $_acte_ccam->_ref_code_ccam->isComplement()
      ) {
        $supp++;
      }
    }
    if (!$irrad || (($irrad + $supp) != count($this->_ref_actes_ccam_facturables))) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule EG4 to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEG4(&$act) {
    $act->_guess_facturable = '1';
    $act->_guess_association = '4';
    $act->_guess_regle_asso = 'EG4';
  }

  /**
   * ### Exception : actes de médecin nucléaire ###
   * * Nombre d'actes : 2
   * * Cas d'utilisation : Les __actes de médecin nucléaire__ sont associés à taux plein, deux actes au plus peuvent
   * être tarfiés. Il en est de même pour un acte de médecine nucléaire associé à un autre acte.
   *
   * @return bool
   */
  protected function checkRuleEG5() {
    /* @todo Identifier les actes de médecin nucélaire */
    $cond = 0;

    if (!$cond) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule EG5 to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEG5(&$act) {
    switch ($act->_position) {
      case 0:
        $act->_guess_facturable = '1';
        $act->_guess_association = '4';
        $act->_guess_regle_asso = 'EG5';
        break;
      case 1:
        $act->_guess_facturable = '1';
        $act->_guess_association = '4';
        $act->_guess_regle_asso = 'EG5';
        break;
      default:
        $act->_guess_facturable = '0';
        $act->_guess_association = '';
        $act->_guess_regle_asso = 'EG5';
    }
  }

  /**
   * ### Exception : forfait de cardilogie, de réanimation, actes de surveillance post-opératoire, actes d'acocuchements ###
   * * Nombre d'actes : 2
   * * Cas d'utilisation : Les __forfait de cardilogie, de réanimation, actes de surveillance post-opératoire (d'un patient de
   * chirurgie cardiaque avec CEC), actes d'acocuchements__ peuvent être associés à taux plein à un seul des actes introduits
   * par la note "facturation : éventuellement en supplément".
   *
   * @return bool
   */
  protected function checkRuleEG6() {
    /* Forfaits de cardiologie : YYYY001, YYYY002 (19.01.02)
     * Forfaits de réanimation : YYYY015, YYYY020 (19.01.11)
     * Surveillance post-op chirurgie cardiaque avec CEC : YYYY108, YYYY118
     * Actes d'accouchements : 09.03.03
     */
    $cond = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      foreach ($_act->_ref_code_ccam->_ref_code_ccam->_ref_notes as $_note) {
        if ($_note->type == 17 && strpos($_note->texte, 'Facturation éventuellement en supplément') !== false) {
          $cond++;
          break;
        }
      }
      foreach ($_act->_ref_code_ccam->chapitres as $_chapter) {
        foreach ($_chapter['rq'] as $_note) {
          if (strpos($_note, 'Facturation : éventuellement en supplément') !== false) {
            $cond++;
          }
        }
      }
    }

    if (!$cond) {
      return false;
    }

    return true;
  }

  /**
   * Apply the association rule EG6 to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEG6(&$act) {
    switch ($act->_position) {
      case 0:
        $act->_guess_facturable = '1';
        $act->_guess_association = '4';
        $act->_guess_regle_asso = 'EG6';
        break;
      case 1:
        $act->_guess_facturable = '1';
        $act->_guess_association = '4';
        $act->_guess_regle_asso = 'EG6';
        break;
      default:
        $act->_guess_facturable = '0';
        $act->_guess_association = '';
        $act->_guess_regle_asso = 'EG6';
    }
  }

  /**
   * ### Exception : actes bucco-dentaires ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Les __actes bucco-dentaires__, y comprit les suppléments autorisés avec ces actes, peuvent
   * être associés à taux plein ente eux ou à eux-même ou à un autre acte, quel que soit le nombre d'actes bucco-dentaires.
   *
   * @return bool
   */
  protected function checkRuleEG7() {
    $nb_bucco_dentaires = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      $classif = reset($_act->_ref_code_ccam->_ref_code_ccam->_ref_activites[$_act->code_activite]->_ref_classif);
      if ($classif->code_regroupement == 'DEN') {
        $nb_bucco_dentaires++;
      }
    }
    if (!$nb_bucco_dentaires) {
      return false;
    }

    $this->_check_rules['EG7'] = array(
      'nb_bucco_dentaires' => $nb_bucco_dentaires
    );

    return true;
  }

  /**
   * Apply the association rule EG7 to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEG7(&$act) {
    $ordered_acts_eg7 = $this->_ordered_acts;
    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      $chapters = $_act->_ref_code_ccam->chapitres;
      if ($_act->_ref_code_ccam->_activite[$_act->code_activite]->_ref_classif->code_regroupement == 'DEN') {
        unset($ordered_acts_eg7[$_act->_id]);
        if ($_act->_id == $act->_id) {
          $act->_position = -1;
        }
      }
      elseif ($chapters[0]['db'] == '000019' && $chapters[1]['db'] == '000002') {
        unset($ordered_acts_eg7[$_act->_id]);
        if ($_act->_id == $act->_id) {
          $act->_position = -1;
        }
      }
    }

    if ($act->_position != -1) {
      $ordered_acts_eg7 = self::orderActsByTarif($ordered_acts_eg7);
      $act->_position = array_search($act->_id, array_keys($ordered_acts_eg7));
    }

    $nb_bucco_dentaires = $this->_check_rules['EG7']['nb_bucco_dentaires'];
    if ($nb_bucco_dentaires == 2 || count($ordered_acts_eg7) == 1) {
      $act->_guess_facturable = '1';
      $act->_guess_association = '4';
      $act->_guess_regle_asso = 'EG7';
    }
    else {
      switch ($act->_position) {
        case -1:
          $act->_guess_facturable = '1';
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EG7';
          break;
        case 0:
          $act->_guess_facturable = '1';
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EG7';
          break;
        case 1:
          $act->_guess_facturable = '1';
          $act->_guess_association = '2';
          $act->_guess_regle_asso = 'EG7';
          break;
        default:
          $act->_guess_facturable = '0';
          $act->_guess_association = '';
          $act->_guess_regle_asso = 'EG7';
      }
    }
  }

  /**
   * ### Exception : actes discontinus ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Actes effectués dans un temps différent et discontinu de la même journée.
   *
   * @return bool
   */
  protected static function checkRuleEH() {
    return false;
  }

  /**
   * Apply the association rule EH to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEH(&$act) {
    /**
     * Trier les actes par moments:
     *    - 1er moment : acte de tarif le + élevé => 1, les autres => 2
     *    - 2ème moment : acte de tarif le + élevé => 5, les autres => 2
     */
  }

  /**
   * ### Exception : actes de radiologie conventionnelle ###
   * * Nombre d'actes : 2, 3, ou 4
   * * Cas d'utilisation : Les __actes de radiologie conventionnelle__ peuvent être associés entre eux (quel que soit
   * leur nombre), ou à d'autres actes.
   *
   * @return bool
   */
  protected function checkRuleEI() {
    $chapters_radio = array(
      '01.01.04.',
      '02.01.03.',
      '04.01.04.',
      '05.01.01.',
      '06.01.03.',
      '07.01.04.',
      '08.01.03.',
      '09.01.03.',
      '11.01.03.',
      '12.01.03.',
      '13.01.01.',
      '14.01.02.',
      '15.01.02.',
      '16.02.02.',
      '17.01.02'
    );
    $nb_radio = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      $chap = $_act->_ref_code_ccam->chapitres;
      if (in_array($chap[2]['rang'], $chapters_radio)) {
        $nb_radio++;
      }
    }

    if (!$nb_radio) {
      return false;
    }

    $this->_check_rules['EI'] = array(
      'nb_radio' => $nb_radio,
    );

    return true;
  }

  /**
   * Apply the association rule EI to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleEI(&$act) {
    $ordered_acts_ei = $this->_ordered_acts;
    $ordered_acts_radio = array();
    $chapters_radio = array(
      '01.01.04.',
      '02.01.03.',
      '04.01.04.',
      '05.01.01.',
      '06.01.03.',
      '07.01.04.',
      '08.01.03.',
      '09.01.03.',
      '11.01.03.',
      '12.01.03.',
      '13.01.01.',
      '14.01.02.',
      '15.01.02.',
      '17.01.02'
    );

    $nb_radio_sein = 0;

    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      $chap = $_act->_ref_code_ccam->chapitres;
      if (in_array($chap[2]['rang'], $chapters_radio)) {
        unset($ordered_acts_ei[$_act->_id]);
        $ordered_acts_radio[$_act->_id] = $_act->getTarifSansAssociationNiCharge();
        if ($_act->_id == $act->_id) {
          $act->_position = -2;
        }
      }
      elseif (in_array($chap[1]['rang'], array('19.02.', '18.02.'))) {
        unset($ordered_acts_ei[$_act->_id]);
        if ($_act->_id == $act->_id) {
          $act->_position = -1;
        }
      }
      elseif (in_array($chap[2]['rang'], array('16.02.01.', '16.02.02.'))) {
        $nb_radio_sein++;
      }
    }

    if ($act->_position == -2) {
      $ordered_acts_radio = self::orderActsByTarif($ordered_acts_radio);
      $act->_position = array_search($act->_id, array_keys($ordered_acts_radio));
    }
    elseif ($act->_position != -1) {
      $ordered_acts_ei = self::orderActsByTarif($ordered_acts_ei);
      $act->_position = array_search($act->_id, array_keys($ordered_acts_ei));
    }

    $nb_radio = $this->_check_rules['EI']['nb_radio'];
    if ($nb_radio_sein == 2) {
      $act->_position = array_search($act->_id, array_keys($this->_ordered_acts));
      switch ($act->_position) {
        case 0:
          $act->_guess_facturable = '1';
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EI';
          break;
        case 1:
          $act->_guess_facturable = '1';
          $act->_guess_association = '2';
          $act->_guess_regle_asso = 'EI';
          break;
        default:
          $act->_guess_facturable = '0';
          $act->_guess_association = '';
          $act->_guess_regle_asso = 'EI';
      }
    }
    else {
      switch ($act->_position) {
        case -1:
          $act->_guess_facturable = '1';
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EI';
          break;
        case 0:
          $act->_guess_facturable = '1';
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EI';
          break;
        case 1:
          $act->_guess_facturable = '1';
          $act->_guess_association = '2';
          $act->_guess_regle_asso = 'EI';
          break;
        default:
          $act->_guess_facturable = '0';
          $act->_guess_association = '';
          $act->_guess_regle_asso = 'EI';
      }
    }
  }
}