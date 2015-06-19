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
  public $activite_anesth;
  public $date;

  /**
   * @var CActeCCAM[]
   */
  protected $_ordered_acts;

  protected $_check_failed_acts = array();

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
   * @var float The total price
   */
  public $_total;

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

  /**
   * @var CCodageCCAM
   */
  public $_codage_sibling;

  protected static $association_rules = array(
    'M'   => 'auto',
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
    $spec->uniques['codable_praticien'] = array('codable_class', 'codable_id', 'praticien_id', 'activite_anesth', 'date');

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  public function getProps() {
    $props = parent::getProps();

    $props['association_rule'] = 'enum list|G1|EA|EB|EC|ED|EE|EF|EG1|EG2|EG3|EG4|EG5|EG6|EG7|EH|EI|GA|GB|G2|M';
    $props['association_mode'] = 'enum list|auto|user_choice default|auto';
    $props['codable_class']    = 'str notNull class';
    $props['codable_id']       = 'ref notNull class|CCodable meta|codable_class';
    $props['praticien_id']     = 'ref notNull class|CMediusers';
    $props['locked']           = 'bool notNull default|0';
    $props['activite_anesth']  = 'bool notNull default|0';
    $props['date']             = 'date notNull';
    $props['_total']           = 'currency';

    return $props;
  }

  public function store() {
    /* Set the default value for activite_anesth because the framework doesn't set it before checking the uniques constraints */
    if (!$this->_id && !$this->activite_anesth) {
      $this->activite_anesth = '0';
    }
    /* Create the CCodageCCAM for the anesthesia activity if the practitioner is an anesthesist */
    if (!$this->_id && !$this->activite_anesth) {
      $this->loadPraticien();
      if ($this->_ref_praticien->isAnesth()) {
        $this->loadCodable();
        self::get($this->_ref_codable, $this->praticien_id, 4, $this->date);
      }
    }

    return parent::store();
  }

  public function loadView() {
    parent::loadView();
    $this->loadPraticien()->loadRefFunction();
    $this->loadCodable();
    $this->loadActesCCAM();
    foreach ($this->_ref_actes_ccam as &$_acte) {
      $_acte->getTarif();
      $_activite = $_acte->_ref_code_ccam->activites[$_acte->code_activite];
      $_phase = $_activite->phases[$_acte->code_phase];

      /* Verification des modificateurs codés */
      foreach ($_phase->_modificateurs as $modificateur) {
        $position = strpos($_acte->modificateurs, $modificateur->code);
        if ($position !== false) {
          if ($modificateur->_double == "1") {
            $modificateur->_checked = $modificateur->code;
          }
          elseif ($modificateur->_double == "2") {
            $modificateur->_checked = $modificateur->code.$modificateur->_double;
          }
          else {
            $modificateur->_checked = null;
          }
        }
        else {
          $modificateur->_checked = null;
        }
      }

      self::precodeModifiers($_phase->_modificateurs, $_acte, $this->_ref_codable);
    }

    if ($this->_ref_praticien->isAnesth()) {
      $this->_codage_sibling = self::get($this->_ref_codable, $this->praticien_id, $this->activite_anesth ? 1 : 4, $this->date);
      $this->_codage_sibling->loadActesCCAM();
    }
  }

  /**
   * Return the CCodageCCAM linked to the given codable and practitioner, and create it if it not exists
   *
   * @param CCodable $codable         The codable object
   * @param integer  $praticien_id    The practitioner id
   * @param integer  $activite        Is the CCodage concern anesthesia activities or other activities
   * @param date     $date            The date
   *
   * @return CCodageCCAM
   */
  public static function get($codable, $praticien_id, $activite = 1, $date = null) {
    if (!$date) {
      switch ($codable->_class) {
        case 'CConsultation':
          $codable->loadRefPlageConsult();
          $date = $codable->_date;
          break;
        case 'COperation':
          $date = $codable->date;
          break;
        case 'CSejour':
          return new CCodageCCAM();
      }
    }

    $codage_ccam = new CCodageCCAM();
    $codage_ccam->codable_class = $codable->_class;
    $codage_ccam->codable_id = $codable->_id;
    $codage_ccam->praticien_id = $praticien_id;
    $codage_ccam->loadPraticien();
    $codage_ccam->activite_anesth = ($activite == 4 && $codage_ccam->_ref_praticien->isAnesth()) ? 1 : 0;
    $codage_ccam->date = $date;
    $codage_ccam->loadMatchingObject();

    if (!$codage_ccam->_id) {
      $codage_ccam->_apply_rules = false;
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
    $where = array();
    $where['object_class'] = " = '$this->codable_class'";
    $where['object_id'] = " = $this->codable_id";
    $where['executant_id'] = " = $this->praticien_id";
    $where['code_activite'] = $this->activite_anesth ? " = 4" : " != 4";
    $where['execution'] = " BETWEEN '$this->date 00:00:00' AND '$this->date 23:59:59'";
    $this->_ref_actes_ccam = $act->loadList($where, "code_association");

    foreach ($this->_ref_actes_ccam as $_acte) {
      if (in_array($_acte->code_acte, $this->_check_failed_acts)) {
        unset($this->_ref_actes_ccam[$_acte->_id]);
        continue;
      }
      $_acte->loadRefCodeCCAM();
    }

    return $this->_ref_actes_ccam;
  }

  public function getTarifTotal() {
    $this->loadActesCCAM();
    $this->_total = 0;

    foreach ($this->_ref_actes_ccam as $_acte) {
      $this->_total += $_acte->getTarif();
    }
  }

  /**
   * Force the update of the rule
   *
   * @param bool $force force the update of the actes
   *
   * @return bool
   */
  function updateRule($force = false) {
    $this->guessRule();
    if ($this->fieldModified('association_rule') || $force) {
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
    $this->completeField('codable_class', 'codable_id', 'praticien_id', 'association_mode', 'association_rule', 'locked', 'activite_anesth', 'date');
    $this->loadOldObject();

    $codable = $this->loadCodable();
    if($codable->_class == 'CConsultation') {
      $codable->loadRefPlageConsult();
      if ($this->date != $codable->_date) {
        return 'Impossible de créer un codage CCAM a une date différente de celle de la consultation';
      }
    }
    if ($codable->_class == 'COperation') {
      if ($this->date != $codable->date) {
        return 'Impossible de créer un codage CCAM a une date différente de celle de l\'intervention';
      }
    }

    if ($this->_old->locked && $this->locked && !CModule::getCanDo('dPpmsi')->edit) {
      return "Codage verrouillé";
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
    if ($this->_id && $this->association_mode != 'auto') {
      return $this->association_rule;
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
    call_user_func(array($this, "checkRule$this->association_rule"));
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
      $_act->_calcul_montant_base = 1;
      $_act->_position = array_search($_act->_id, array_keys($this->_ordered_acts));
      $this->applyRule($this->association_rule, $_act);
      if ($msg = $_act->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
        if (!in_array($_act->code_acte, $this->_check_failed_acts)) {
          $this->_check_failed_acts[] = $_act->code_acte;
          $this->updateRule();
          break;
        }
      }
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
   * Count the number of modifiers F, U, P and S coded
   *
   * @param CActeCCAM &$act The act
   *
   * @return integer
   */
  public static function countExclusiveModifiers(&$act) {
    $act->getLinkedActes(true, true, true, true);
    $exclusive_modifiers = array('F', 'U', 'P', 'S');
    $count_exclusive_modifiers = count(array_intersect($act->_modificateurs, $exclusive_modifiers));

    foreach ($act->_linked_actes as $_linked_act) {
      $count_exclusive_modifiers += count(array_intersect($_linked_act->_modificateurs, $exclusive_modifiers));
    }
    $act->_exclusive_modifiers = $count_exclusive_modifiers;

    return $count_exclusive_modifiers;
  }

  /**
   * Check if a modifier has been checked on the linked acts of the given act
   *
   * @param string    $mod  The modifier
   * @param CActeCCAM &$act the act
   *
   * @return boolean
   */
  public static function isModifierchecked($mod, &$act) {
    $act->getLinkedActes(true, true, true, true);

    $modifier = false;
    foreach ($act->_linked_actes as $_linked_act) {
      if (in_array($mod, $_linked_act->_modificateurs)) {
        $modifier = true;
      }
    }

    return $modifier;
  }

  /**
    * Check the modifiers of the given act
    *
    * @param CObject   &$modifiers The modifiers to check
    * @param CActeCCAM &$act       The dateTime of the execution of the act
    * @param CCodable  $codable    The codable
    *
    * @return void
    */
  public static function precodeModifiers(&$modifiers, &$act, $codable) {
    $date = CMbDT::date(null, $act->execution);
    $time = CMbDT::time(null, $act->execution);
    $act->loadRefExecutant();
    $act->_ref_executant->loadRefDiscipline();
    $discipline = $act->_ref_executant->_ref_discipline;
    $patient = $codable->loadRefPatient();
    $patient->evalAge();
    $checked = 0;
    $spe_gyneco = $spe_gyneco = array(
      'GYNECOLOGIE MEDICALE, OBSTETRIQUE',
      'GYNECOLOGIE-OBSTETRIQUE',
      'MEDECINE DE LA REPRODUCTION ET GYNECOLOGIE MEDICAL'
    );
    $spe_gen_pediatre = array("MEDECINE GENERALE", "PEDIATRIE");
    $count_exclusive_modifiers = self::countExclusiveModifiers($act);
    $store_act = 0;
    $modifiers_to_add = "";
    $achieved = CMbDate::achievedDurations($patient->naissance, CMbDT::date(null, $act->execution));
    $patient_age = $achieved["year"];

    foreach ($modifiers as $_modifier) {
      switch ($_modifier->code) {
        case 'A':
          $checked = ($patient_age < 4 || $patient_age >= 80);
          $_modifier->_state = $checked ? 'prechecked' : 'not_recommended';
          break;
        case 'E':
          $checked = $patient->_annees < 5;
          $_modifier->_state = $checked ? 'prechecked' : 'not_recommended';
          break;
        case 'F':
          $checked = (($count_exclusive_modifiers == 1 && $_modifier->_checked) || $count_exclusive_modifiers == 0) &&
            (CMbDT::transform('', $act->execution, '%w') == 0 || CMbDate::isHoliday($date))
            && ($time > '08:00:00' && $time < '20:00:00');
          if ($checked) {
            $_modifier->_state = 'prechecked';
          }
          elseif (($count_exclusive_modifiers == 1 && $_modifier->_checked) || $count_exclusive_modifiers > 0) {
            $_modifier->_state = 'forbidden';
          }
          else {
            $_modifier->_state = 'not_recommended';
          }
          break;
        case "J":
          $checked = $codable->_class == 'COperation' && CAppUI::pref('precode_modificateur_J');
          $_modifier->_state = $checked ? 'prechecked' : null;
          break;
        case 'K':
          $checked = !$act->montant_depassement && ($act->_ref_executant->secteur == 1 || ($act->_ref_executant->secteur == 2 && $patient->cmu) ||
              $act->_ref_executant->contrat_acces_soins || $act->_ref_executant->option_coordination);
          if ($checked) {
            $_modifier->_state = 'prechecked';
          }
          elseif (!in_array($discipline, $spe_gyneco)) {
            $_modifier->_state = 'not_recommended';
          }
          if (self::isModifierchecked('K', $act) && !$act->montant_depassement) {
            $checked = true;
          }
          break;
        case 'L':
          if (self::isModifierchecked('L', $act)) {
            $_modifier->_state = 'prechecked';
            $checked = true;
          }
          break;
        case 'M':
          $checked = 0;
          if (!in_array($discipline->text, $spe_gen_pediatre)) {
            $_modifier->_state = 'not_recommended';
          }
          break;
        case 'N':
          $checked = $patient->_annees < 13;
          $_modifier->_state = $checked ? 'prechecked' : 'not_recommended';
          break;
        case 'P':
          $checked = (($count_exclusive_modifiers == 1 && $_modifier->_checked) || $count_exclusive_modifiers == 0) &&
            in_array($discipline->text, $spe_gen_pediatre) &&
            ($time > "20:00:00" && $time < "23:59:59");
          if ($checked) {
            $_modifier->_state = 'prechecked';
          }
          elseif (($count_exclusive_modifiers == 1 && $_modifier->_checked) || $count_exclusive_modifiers > 0) {
            $_modifier->_state = 'forbidden';
          }
          else {
            $_modifier->_state = 'not_recommended';
          }
          break;
        case 'R':
          if (self::isModifierchecked('R', $act)) {
            $_modifier->_state = 'prechecked';
            $checked = true;
          }
          break;
        case 'S':
          $checked = (
              in_array($discipline->text, $spe_gen_pediatre) ||
              ($codable->_class == "COperation" && $codable->_lu_type_anesth)
              ) && ($time >= "00:00:00" && $time < "08:00:00") &&
              (($count_exclusive_modifiers == 1 && $_modifier->_checked) || $count_exclusive_modifiers == 0);
          if ($checked) {
            $_modifier->_state = 'prechecked';
          }
          elseif (($count_exclusive_modifiers == 1 && $_modifier->_checked) || $count_exclusive_modifiers > 0) {
            $_modifier->_state = 'forbidden';
          }
          else {
            $_modifier->_state = 'not_recommended';
          }
          break;
        case 'U':
          $checked = (($count_exclusive_modifiers == 1 && $_modifier->_checked) || $count_exclusive_modifiers == 0) &&
            !in_array($discipline->text, $spe_gen_pediatre) &&
            ($time > '20:00:00' || $time < '08:00:00');
          if ($checked) {
            $_modifier->_state = 'prechecked';
          }
          elseif (($count_exclusive_modifiers == 1 && $_modifier->_checked) || $count_exclusive_modifiers > 0) {
            $_modifier->_state = 'forbidden';
          }
          else {
            $_modifier->_state = 'not_recommended';
          }
          break;
        case "7":
          $checked = CAppUI::pref('precode_modificateur_7') &&
            $codable->_class == 'COperation' && (isset($codable->anesth_id) || $act->_ref_executant->isAnesth());
          if ($checked) {
            $_modifier->_state = 'prechecked';
          }
          else {
            $_modifier->_state = ($codable->_class == 'COperation' && (isset($codable->anesth_id) || $act->_ref_executant->isAnesth())) ? null : 'not_recommended';
          }
          break;
        default:
          $checked = 0;
          break;
      }

      if (!$act->_id && !isset($_modifier->_checked)) {
        $_modifier->_checked = $checked;
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
      $act->facturable = '0';
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
  protected function checkRuleM() {
    if (count($this->_ref_actes_ccam_facturables) > 0) {
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
  protected function applyRuleM(&$act) {
    $act->completeField('facturable', 'code_association');
    $act->_guess_facturable = $act->facturable;
    $act->_guess_association = $act->code_association;
    $act->_guess_regle_asso = 'M';
  }

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
    if (count($this->_ref_actes_ccam_facturables) >= 2) {
      return true;
    }
    return false;
  }

  /**
   * Apply the association rule G2 to the given act
   *
   * @param CActeCCAM &$act The act
   *
   * @return void
   */
  protected function applyRuleG2(&$act) {
    $ordered_acts_g2 = $this->_ordered_acts;
    foreach ($this->_ref_actes_ccam_facturables as $_acte_ccam) {
      if (
          $_acte_ccam->_ref_code_ccam->isSupplement() ||
          $_acte_ccam->_ref_code_ccam->isComplement()
      ) {
        unset($ordered_acts_g2[$_acte_ccam->_id]);
        if ($_acte_ccam->_id == $act->_id) {
          $act->_position = -1;
        }
      }
    }

    if ($act->_position != -1) {
      self::orderActsByTarif($ordered_acts_g2);
      $act->_position = array_search($act->_id, array_keys($ordered_acts_g2));
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
      if (isset($chapters[2]) && in_array($chapters[2]['rang'], $chapters_echo)) {
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
      if (isset($chapters[2]) && in_array($chapters[2]['rang'], $chapters_scano)) {
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
      elseif (isset($chapters[2]) && in_array($chapters[2]['rang'], $chapters_remno)) {
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
   * ### Exception actes de radiologie vasculaire et imagerie conventionnelle ###
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
      if (isset($chapters[3]) && $chapters[3]['rang'] == '19.01.09.02.') {
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
      $chapters = $_act->_ref_code_ccam->chapitres;
      if ((isset($chapters[2]) && in_array($chapters[2]['rang'], $chapters_anapath)) || (isset($chapters[1]) && in_array($chapters[1]['rang'], $chapters_anapath))) {
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
      $chapters = $_act->_ref_code_ccam->chapitres;
      if ((isset($chapters[2]) && in_array($chapters[2]['rang'], $chapters_anapath)) || (isset($chapters[1]) && in_array($chapters[1]['rang'], $chapters_anapath))) {
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
      if (isset($chapters[3]) && in_array($chapters[3]['rang'], array('01.01.01.01.', '01.01.01.02.', '01.01.01.03.'))) {
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
      if (isset($chapters[3]) && in_array($chapters[3]['rang'], array('01.01.01.01.', '01.01.01.02.', '01.01.01.03.'))) {
        unset($ordered_acts_eg3[$_acte_ccam->_id]);
        if ($_acte_ccam->_id == $act->_id) {
          $act->_position = -1;
        }
      }
      elseif (isset($chapters[1]) && $chapters[1]['rang'] == '19.02.') {
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
      if (isset($chapters[2]) && in_array($chapters[2]['rang'], array('17.04.02.', '19.01.10.'))) {
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
    $chapters_dentaire = array(
      '07.01.04.01.',
      '07.01.08.01.',
      '07.02.02.01.',
      '07.02.02.03.',
      '07.02.02.04.',
      '07.02.02.05.',
      '07.02.02.06.',
      '07.02.02.08.',
      '07.02.02.09.',
      '07.02.02.10.',
      '07.02.02.11.',
      '07.02.02.12.',
      '07.02.02.15.',
      '07.02.03.',
      '07.02.05.',
      '07.02.06.10.',
      '11.02.05.02.',
      '11.02.05.03.',
      '11.02.05.04.',
      '18.02.07.01.',
      '18.02.07.06.'
    );
    $exclude_codes = array('HJQD001');
    $includes_codes = array('LBGA280', 'LBGA441', 'LBGA354',
      'LBGA049', 'LBGA004', 'LBGA003', 'LBGA002', 'LBGA006',
      'LBGA007', 'LBGA008', 'LBGA009', 'LBGA139', 'LBGA052',
      'HBLD057', 'HBLD078', 'HBLD056', 'LBGA168', 'HBLD084',
      'HBLD084', 'HBMP001', 'LBLD014'
    );

    $nb_bucco_dentaires = 0;
    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      $chapters = $_act->_ref_code_ccam->chapitres;
      if (
        !in_array($_act->code_acte, $exclude_codes) && ((isset($chapters[2]) && in_array($chapters[2]['rang'], $chapters_dentaire)) ||
          (isset($chapters[3]) && in_array($chapters[3]['rang'], $chapters_dentaire)) || in_array($_act->code_acte, $includes_codes))
      ) {
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
    $chapters_dentaire = array(
      '07.01.04.01.',
      '07.01.08.01.',
      '07.02.02.01.',
      '07.02.02.03.',
      '07.02.02.04.',
      '07.02.02.05.',
      '07.02.02.06.',
      '07.02.02.08.',
      '07.02.02.09.',
      '07.02.02.10.',
      '07.02.02.11.',
      '07.02.02.12.',
      '07.02.02.15.',
      '07.02.03.',
      '07.02.05.',
      '07.02.06.10.',
      '11.02.05.02.',
      '11.02.05.03.',
      '11.02.05.04.',
      '18.02.07.01.',
      '18.02.07.06.'
    );
    $exclude_codes = array('HJQD001');
    $includes_codes = array('LBGA280', 'LBGA441', 'LBGA354',
      'LBGA049', 'LBGA004', 'LBGA003', 'LBGA002', 'LBGA006',
      'LBGA007', 'LBGA008', 'LBGA009', 'LBGA139', 'LBGA052',
      'HBLD057', 'HBLD078', 'HBLD056', 'LBGA168', 'HBLD084',
      'HBLD084', 'HBMP001', 'LBLD014'
    );

    foreach ($this->_ref_actes_ccam_facturables as $_act) {
      $chapters = $_act->_ref_code_ccam->chapitres;
      if (
        !in_array($_act->code_acte, $exclude_codes) && ((isset($chapters[2]) && in_array($chapters[2]['rang'], $chapters_dentaire)) ||
          (isset($chapters[3]) && in_array($chapters[3]['rang'], $chapters_dentaire)) || in_array($_act->code_acte, $includes_codes))
      ) {
        unset($ordered_acts_eg7[$_act->_id]);
        if ($_act->_id == $act->_id) {
          $act->_position = -1;
        }
      }
      elseif (isset($chapters[1]) && $chapters[1]['rang'] == '19.02.') {
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
    if (!count($ordered_acts_eg7) || count($ordered_acts_eg7) == 1) {
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
      $chapters = $_act->_ref_code_ccam->chapitres;
      if (isset($chapters[2]) && in_array($chapters[2]['rang'], $chapters_radio)) {
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
      $chapters = $_act->_ref_code_ccam->chapitres;
      if (isset($chapters[2]) && in_array($chapters[2]['rang'], $chapters_radio)) {
        unset($ordered_acts_ei[$_act->_id]);
        $ordered_acts_radio[$_act->_id] = $_act->getTarifSansAssociationNiCharge();
        if ($_act->_id == $act->_id) {
          $act->_position = -2;
        }
      }
      elseif (isset($chapters[1]) && in_array($chapters[1]['rang'], array('19.02.', '18.02.'))) {
        unset($ordered_acts_ei[$_act->_id]);
        if ($_act->_id == $act->_id) {
          $act->_position = -1;
        }
      }
      elseif (isset($chapters[2]) && in_array($chapters[2]['rang'], array('16.02.01.', '16.02.02.'))) {
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