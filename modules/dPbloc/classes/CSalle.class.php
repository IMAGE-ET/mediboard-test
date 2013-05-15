<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

class CSalle extends CMbObject {
  public $salle_id;
  
  // DB references
  public $bloc_id;
  
  // DB Fields
  public $nom;
  public $stats;
  public $dh;

  /** @var CBlocOperatoire */
  public $_ref_bloc;

  /** @var CPlageOp[] */
  public $_ref_plages;

  /** @var COperation[] */
  public $_ref_urgences;

  /** @var COperation[] */
  public $_ref_deplacees;
  
  // Form fields
  public $_blocage = array();
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sallesbloc';
    $spec->key   = 'salle_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["operations"]   = "COperation salle_id";
    $backProps["plages_op"]    = "CPlageOp salle_id";
    $backProps["check_lists"]  = "CDailyCheckList object_id";
    $backProps["blocages"]     = "CBlocage salle_id";
    $backProps["commentaires"] = "CCommentairePlanning salle_id";
    $backProps["check_list_categories"] = "CDailyCheckItemCategory target_id";
    $backProps["check_list_type_links"] = "CDailyCheckListTypeLink object_id";
    return $backProps;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["bloc_id"] = "ref notNull class|CBlocOperatoire";
    $props["nom"]     = "str notNull seekable";
    $props["stats"]   = "bool notNull";
    $props["dh"]      = "bool notNull default|0";
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $bloc = $this->loadRefBloc();
    
    $where = array(
      'group_id' => "= '$bloc->group_id'"
    );
    $this->_view = "";
    if ($bloc->countList($where) > 1) {
      $this->_view = $bloc->nom.' - ';
    }
    $this->_view .= $this->nom;
    $this->_shortview = $this->nom;
  }

  /**
   * Load list overlay for current group
   *
   * @see parent::loadGroupList()
   *
   */
  function loadGroupList($where = array(), $order = 'bloc_id, nom', $limit = null, $groupby = null, $ljoin = array()) {
    $list_blocs = CGroups::loadCurrent()->loadBlocs(PERM_READ, false);
    
    // Filtre sur l'établissement
    $where[] = "bloc_id ".CSQLDataSource::prepareIn(array_keys($list_blocs));
    
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }

  function getPerm($permType) {
    $this->loadRefBloc();
    return $this->_ref_bloc->getPerm($permType) && parent::getPerm($permType);
  }

  /**
   * Chargement du bloc opératoire
   *
   * @return CBlocOperatoire
   */
  function loadRefBloc() {
    return $this->_ref_bloc = $this->loadFwdRef("bloc_id", true);
  }
  
  function loadRefsFwd(){
    $this->loadRefBloc();
  }
  
  /**
   * Charge la liste de plages et opérations pour un jour donné
   * Analogue à CMediusers::loadRefsForDay
   *
   * @param string $date Date to look for
   *
   * @return void
   */
  function loadRefsForDay($date) {
    // Plages d'opérations
    $plages = new CPlageOp;
    $where = array();
    $where["date"] = "= '$date'";
    $where["salle_id"] = "= '$this->_id'";
    $order = "debut";
    $this->_ref_plages = $plages->loadList($where, $order);
    foreach ($this->_ref_plages as &$plage) {
      /** @var CPlageOp $plage */
      $plage->loadRefs();
      $plage->loadRefsNotes();
      $plage->loadAffectationsPersonnel();
      $plage->_unordered_operations = array();
      foreach ($plage->_ref_operations as &$operation) {
        $operation->loadRefAnesth();
        $operation->loadRefChir();
        $operation->loadRefPatient();
        $operation->loadExtCodesCCAM();
        $operation->loadRefPlageOp();

        if (CAppUI::conf("dPbloc CPlageOp chambre_operation")) {
          $operation->loadRefAffectation();
        }
        
        // Extraire les interventions non placées
        if ($operation->rank == 0) {
          $plage->_unordered_operations[$operation->_id] = $operation;
          unset($plage->_ref_operations[$operation->_id]);
        }
      }
    }
    
    // Interventions déplacés
    $deplacees = new COperation;
    $ljoin = array();
    $ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
    $where = array();
    $where["operations.plageop_id"] = "IS NOT NULL";
    $where["plagesop.salle_id"]     = "!= operations.salle_id";
    $where["plagesop.date"]         = "= '$date'";
    $where["operations.salle_id"]   = "= '$this->_id'";
    $order = "operations.time_operation";
    $this->_ref_deplacees = $deplacees->loadList($where, $order, null, null, $ljoin);
    foreach ($this->_ref_deplacees as &$deplacee) {
      /** @var COperation $deplacee */
      $deplacee->loadRefChir();
      $deplacee->loadRefPatient();
      $deplacee->loadExtCodesCCAM();
      $deplacee->loadRefPlageOp();
    }

    // Urgences
    $urgences = new COperation;
    $where = array();
    $where["date"]     = "= '$date'";
    $where["salle_id"] = "= '$this->_id'";
    $order = "time_operation, chir_id";
    $this->_ref_urgences = $urgences->loadList($where, $order);
    foreach ($this->_ref_urgences as &$urgence) {
      /** @var COperation $urgence */
      $urgence->loadRefChir();
      $urgence->loadRefPatient();
      $urgence->loadExtCodesCCAM();
      $urgence->loadRefPlageOp();
    }
  }

  /**
   * @return CAlert[]
   */
  function loadRefsAlertesIntervs() {
    $alerte = new CAlert();
    $ljoin = array();
    $ljoin["operations"] = "operations.operation_id = alert.object_id";
    $ljoin["plagesop"]   = "plagesop.plageop_id = operations.plageop_id";
    $where = array();
    $where["alert.object_class"] = "= 'COperation'";
    $where["alert.tag"] = "= 'mouvement_intervention'";
    $where["alert.handled"]   = "= '0'";
    $where[] = "operations.salle_id = '$this->salle_id'
      OR plagesop.salle_id = '$this->salle_id'
      OR (plagesop.salle_id IS NULL AND operations.salle_id IS NULL)";
    $order = "operations.date, operations.chir_id";
    return $this->_alertes_intervs = $alerte->loadList($where, $order, null, null, $ljoin);
  }

  /**
   * @param string $date
   *
   * @return CBlocage[]
   */
  function loadRefsBlocages($date = "now") {
    $blocage = new CBlocage();
    
    if ($date == "now") {
      $date = CMbDT::date();
    }
    
    $where = array();
    $where["salle_id"] = "= '$this->_id'";
    $where[] = "'$date' BETWEEN deb AND fin";
    
    return $blocage->loadList($where);
  }

  /**
   * @param int $salle_id
   * @param int $bloc_id
   *
   * @return self[]
   */
  static function getSallesStats($salle_id = null, $bloc_id = null) {
    $group_id = CGroups::loadCurrent()->_id;

    $where = array();
    $where['stats'] = " = '1'";

    $ljoin = array();

    if ($salle_id) {
      $where['salle_id'] = " = '$salle_id'";
    }
    elseif ($bloc_id) {
      $where['bloc_id'] = "= '$bloc_id'";
    }
    else {
      $where['bloc_operatoire.group_id'] = "= '$group_id'";
      $ljoin['bloc_operatoire'] = 'bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id';
    }

    $salle = new self;
    return $salle->loadList($where, null, null, null, $ljoin);
  }
}
