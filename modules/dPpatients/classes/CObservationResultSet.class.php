<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Observation result set, based on the HL7 OBR message
 * http://www.interfaceware.com/hl7-standard/hl7-segment-OBR.html
 */
class CObservationResultSet extends CMbObject {
  public $observation_result_set_id;
  
  public $patient_id;
  public $datetime;
  public $context_class;
  public $context_id;

  /** @var CMbObject */
  public $_ref_context;

  /** @var CPatient */
  public $_ref_patient;

  /** @var CObservationResult[] */
  public $_ref_results;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "observation_result_set";
    $spec->key   = "observation_result_set_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["patient_id"]    = "ref notNull class|CPatient";
    $props["datetime"]      = "dateTime notNull";
    $props["context_class"] = "str notNull";
    $props["context_id"]    = "ref class|CMbObject meta|context_class";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["observation_results"] = "CObservationResult observation_result_set_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->getFormattedValue("datetime");
  }

  /**
   * @param bool $cache Utilisation du cache
   *
   * @return CMbObject
   */
  function loadRefContext($cache = true) {
    return $this->_ref_context = $this->loadFwdRef("context_id", $cache);
  }

  /**
   * @param bool $cache Utilisation du cache
   *
   * @return CPatient
   */
  function loadRefPatient($cache = true) {
    return $this->_ref_patient = $this->loadFwdRef("patient_id", $cache);
  }

  /**
   * @return CObservationResult[]
   */
  function loadRefsResults(){
    return $this->_ref_results = $this->loadBackRefs("observation_results");
  }

  /**
   * Get observation results for this object
   *
   * @param CMbObject $object Reference object
   * @param bool      $utf8   Encode data int UTF-8
   *
   * @return array|CObservationResultSet[]
   */
  static function getResultsFor(CMbObject $object, $utf8 = true) {
    $request = new CRequest();
    $request->addTable("observation_result");
    $request->addSelect("*");
    $request->addLJoin(
      array(
        "observation_result_set" => "observation_result_set.observation_result_set_id = observation_result.observation_result_set_id",
        "user_log"               => "observation_result_set.observation_result_set_id = user_log.object_id AND
                                     user_log.object_class = 'CObservationResultSet' AND user_log.type = 'create'",
        "users"                  => "users.user_id = user_log.user_id",
      )
    );
    $request->addWhere(
      array(
        "observation_result_set.context_class" => "= '$object->_class'",
        "observation_result_set.context_id"    => "= '$object->_id'",
      )
    );
    $request->addOrder("observation_result_set.datetime");
    $request->addOrder("observation_result.observation_result_id");
    
    $results = $object->_spec->ds->loadList($request->makeSelect());
    
    $times = array();
    $data = array();
    
    foreach ($results as $_result) {
      $_time = CMbDate::toUTCTimestamp($_result["datetime"]);
      $times[$_time] = $_result["datetime"];

      $unit_id = $_result["unit_id"] ? $_result["unit_id"] : "none";

      $label = null;
      if ($_result["label_id"]) {
        $label_obj = new CSupervisionGraphAxisValueLabel();
        $label_obj->load($_result["label_id"]);
        $label = $label_obj->title;
      }

      $float_value = $_result["value"];
      $float_value = CMbFieldSpec::checkNumeric($float_value, false);

      $_user_name = $_result["user_first_name"]." ".$_result["user_last_name"];

      $data[$_result["value_type_id"]][$unit_id][] = array(
        0           => $_time,
        1           => $float_value,
        "ts"        => $_time,
        "value"     => $_result["value"],
        "datetime"  => $_result["datetime"],
        "file_id"   => $_result["file_id"],
        "set_id"    => $_result["observation_result_set_id"],
        "result_id" => $_result["observation_result_id"],
        "label_id"  => $_result["label_id"],
        "label"     => $utf8 ? utf8_encode($label) : $label,
        "user_id"   => $_result["user_id"],
        "user"      => $utf8 ? utf8_encode($_user_name) : $_user_name,
      );
    }
    
    return array(
      $data,
      $times,
    );
  }

  /**
   * Donne les heures limites d'une intervention
   *
   * @param COperation $interv Reference interv
   *
   * @return array
   */
  static function getLimitTimes(COperation $interv) {
    $round_minutes = 10;
    $round = $round_minutes * 60000; // FIXME

    $sejour = $interv->loadRefSejour();

    // Cas du partogramme
    if ($sejour->grossesse_id) {
      $grossesse = $sejour->loadRefGrossesse();

      // Debut = debut travail OU entree salle
      if ($grossesse->datetime_debut_travail) {
        $time_debut_op_iso = $grossesse->datetime_debut_travail;
      }
      else {
        $time_min = $interv->entree_salle;
        $date = CMbDT::date($interv->_datetime);
        $time_debut_op_iso = "$date $time_min";
      }

      // Fin = fin accouchement OU debut+1 heure OU maintenant
      if ($grossesse->datetime_accouchement) {
        $time_fin_op_iso = $grossesse->datetime_accouchement;
      }
      else {
        $time_fin_op_iso = max(CMbDT::dateTime(), CMbDT::dateTime("+1 HOUR", $time_debut_op_iso));
      }
    }

    // Cas d'une interv normale
    else {
      $time_min = $interv->entree_salle;
      $time_max = CMbDT::time("+".CMbDT::minutesRelative("00:00:00", $interv->temp_operation)." MINUTES", $interv->entree_salle);

      $date = CMbDT::date($interv->_datetime);

      $fin = CValue::first($interv->sortie_salle, $interv->fin_op, $interv->retrait_garrot);
      if ($fin) {
        $time_max = max($time_max, $fin);
      }

      $time_debut_op_iso = "$date $time_min";
      $time_fin_op_iso   = "$date $time_max";
    }

    $timestamp_min = floor(CMbDate::toUTCTimestamp($time_debut_op_iso) / $round) * $round;
    $timestamp_max =  ceil(CMbDate::toUTCTimestamp($time_fin_op_iso  ) / $round) * $round;

    return array(
      $timestamp_min,
      $timestamp_max,
      $time_debut_op_iso,
      $time_fin_op_iso,
    );
  }

  /**
   * Chargement des graphiques d'intervention
   *
   * @param COperation $interv  Intervention
   *
   * @param int        $pack_id Pack de graphiques
   *
   * @return array
   */
  static function buildGraphs(COperation $interv, $pack_id) {
    list($results, /*$times*/) = CObservationResultSet::getResultsFor($interv);

    list (
      $time_min,
      $time_max,
      $time_debut_op_iso,
      $time_fin_op_iso,
    ) = self::getLimitTimes($interv);

    $pack = new CSupervisionGraphPack();
    $pack->load($pack_id);
    $graph_links = $pack->loadRefsGraphLinks();

    $graphs = array();
    foreach ($graph_links as $_gl) {
      $_go = $_gl->loadRefGraph();
      $graphs[] = $_go;

      if ($_go instanceof CSupervisionGraph) {
        $_go->buildGraph($results, $time_min, $time_max);
      }
      elseif ($_go instanceof CSupervisionTimedData) {
        $_go->loadTimedData($results, $time_min, $time_max);
      }
      elseif ($_go instanceof CSupervisionTimedPicture) {
        $_go->loadTimedPictures($results, $time_min, $time_max);
      }
      elseif ($_go instanceof CSupervisionInstantData) {
        $_go->loadRefValueType();
        $_go->loadRefValueUnit();
      }
    }

    /*
    $graph_object = new CSupervisionGraph;
    $graph_objects = $graph_object->loadList(array(
      "disabled" => "= '0'",
    ));

    $graphs = array();
    foreach ($graph_objects as $_go) {
      $graphs[] = $_go->buildGraph($results, $time_min, $time_max);
    }*/

    $yaxes_count = 0;
    foreach ($graphs as $_graph) {
      if ($_graph instanceof CSupervisionGraph) {
        $yaxes_count = max($yaxes_count, count($_graph->_graph_data["yaxes"]));
      }
    }

    foreach ($graphs as $_graph) {
      if ($_graph instanceof CSupervisionGraph) {
        if (count($_graph->_graph_data["yaxes"]) < $yaxes_count) {
          $_graph->_graph_data["yaxes"] = array_pad(
            $_graph->_graph_data["yaxes"],
            $yaxes_count,
            CSupervisionGraphAxis::$default_yaxis
          );
        }
      }
    }

    return array(
      $graphs, $yaxes_count-1,
      $time_min, $time_max,
      $time_debut_op_iso, $time_fin_op_iso,
    );
  }

  /**
   * Get chronological list
   *
   * @param COperation $interv  Intervention
   * @param int        $pack_id Pack ID
   *
   * @return CObservationResultSet[]
   */
  static function getChronological(COperation $interv, $pack_id) {
    $result_set = new self();
    $where = array(
      "observation_result_set.context_class" => "= '$interv->_class'",
      "observation_result_set.context_id"    => "= '$interv->_id'",
    );
    $order = array(
      "observation_result_set.datetime",
      "observation_result_set.observation_result_set_id"
    );

    $pack = new CSupervisionGraphPack();
    $pack->load($pack_id);
    $graph_links = $pack->loadRefsGraphLinks();

    $list_by_datetime = array();

    $graphs = self::massLoadFwdRef($graph_links, "graph_id");

    /** @var self[] $list */
    $list = $result_set->loadList($where, $order);
    $grid = array();

    // Build the data structure

    $count = 0;
    $labels = array();
    foreach ($graphs as $_graph) {
      if ($_graph instanceof CSupervisionGraph) {
        $_axes = $_graph->loadRefsAxes();

        self::massCountBackRefs($_axes, "series");

        foreach ($_axes as $_axis) {
          $_series = $_axis->loadRefsSeries();

          $count += count($_series);

          foreach ($_series as $_serie) {
            $labels[] = $_serie;
          }
        }
      }
      elseif (
        $_graph instanceof CSupervisionTimedData ||
        $_graph instanceof CSupervisionTimedPicture
      ) {
        $count++;
        $labels[] = $_graph;
      }
    }

    self::massCountBackRefs($list, "observation_results");

    // Fill the data structure
    foreach ($list as $_set) {
      $results = $_set->loadRefsResults();

      self::massLoadFwdRef($results, "file_id");

      foreach ($results as $_result) {
        $_result->loadRefFile();
        $_result->loadRefValueUnit();
      }

      $p = 0;
      $_row = array_fill(0, $count, null);

      foreach ($graphs as $_graph) {
        if ($_graph instanceof CSupervisionGraph) {
          $_axes = $_graph->_ref_axes;

          foreach ($_axes as $_axis) {
            $_series = $_axis->_ref_series;

            foreach ($_series as $_serie) {
              foreach ($results as $_result) {
                if (
                    $_result->value_type_id == $_serie->value_type_id &&
                    $_result->unit_id       == $_serie->value_unit_id
                ) {
                  $_row[$p] = $_result;
                }
              }

              $p++;
            }
          }
        }
        elseif (
            $_graph instanceof CSupervisionTimedData ||
            $_graph instanceof CSupervisionTimedPicture
        ) {
          foreach ($results as $_result) {
            if (
                $_result->value_type_id == $_graph->value_type_id &&
                $_result->unit_id       == null
            ) {
              $_row[$p] = $_result;
            }
          }

          $p++;
        }
      }

      $grid[$_set->datetime] = $_row;
      $list_by_datetime[$_set->datetime] = $_set;
    }

    return array($list, $grid, $graphs, $labels, $list_by_datetime);
  }

  static function buildEventsGrid(COperation $interv, $time_debut_op_iso, $time_fin_op_iso, $time_min , $time_max) {
    // ---------------------------------------------------
    // Gestes, Medicaments, Perfusions peranesth
    $evenements = array(
      "CAnesthPerop"                 => array(),
      "CAffectationPersonnel"        => array(),
    );

    // Personnel de l'interv
    $interv->loadAffectationsPersonnel();
    foreach ($interv->_ref_affectations_personnel as $affectations) {
      foreach ($affectations as $_affectation) {
        if (!$_affectation->debut || !$_affectation->fin) {
          continue;
        }

        $evenements["CAffectationPersonnel"][$_affectation->_id] = array(
          "icon" => null,
          "label" => $_affectation->_ref_personnel,
          "unit"  => null,
          "alert" => false,
          "datetime" => $_affectation->debut,
          "position" => CSupervisionTimedEntity::getPosition($_affectation->debut, $time_min, $time_max),
          "width" => CSupervisionTimedEntity::getWidth($_affectation->debut, $_affectation->fin, $time_min, $time_max),
          "object" => $_affectation,
          "editable" => false,
        );
      }
    }

    // Personnel de la plage
    $plageop = $interv->_ref_plageop;
    $plageop->loadAffectationsPersonnel();
    foreach ($plageop->_ref_affectations_personnel as $affectations) {
      foreach ($affectations as $_affectation) {
        if (!$_affectation->debut || !$_affectation->fin) {
          continue;
        }

        $evenements["CAffectationPersonnel"][$_affectation->_id] = array(
          "icon" => null,
          "label" => $_affectation->_ref_personnel,
          "unit"  => null,
          "alert" => false,
          "datetime" => $_affectation->debut,
          "position" => CSupervisionTimedEntity::getPosition($_affectation->debut, $time_min, $time_max),
          "width" => CSupervisionTimedEntity::getWidth($_affectation->debut, $_affectation->fin, $time_min, $time_max),
          "object" => $_affectation,
          "editable" => false,
        );
      }
    }

    // Gestes perop
    $interv->loadRefsAnesthPerops();
    foreach ($interv->_ref_anesth_perops as $_perop) {
      $evenements["CAnesthPerop"][$_perop->_id] = array(
        "icon" => null,
        "label" => $_perop->libelle,
        "unit"  => null,
        "alert" => $_perop->incident,
        "datetime" => $_perop->datetime,
        "position" => CSupervisionTimedEntity::getPosition($_perop->datetime, $time_min, $time_max),
        "object" => $_perop,
        "editable" => true,
      );
    }

    // Lignes de medicaments et d'elements
    $sejour = $interv->loadRefSejour();
    $prescription = $sejour->loadRefPrescriptionSejour();

    if ($prescription->_id) {
      $lines = $prescription->loadPeropLines(false);

      foreach ($lines as $_line_array) {
        $_line = $_line_array["object"];

        $key = "CPrescription._chapitres.$_line->_chapitre";
        if (!isset($evenements[$key])) {
          $evenements[$key] = array(
            "subitems" => array(),
            "icon"     => $_line->_chapitre
          );
        }

        // Build view
        $_subkey = array(
          "line"  => $_line,
          "label" => "",
        );
        $_view = "";
        if ($_line instanceof CPrescriptionLineElement) {
          $_view = $_line->_view;
        }
        elseif ($_line instanceof CPrescriptionLineMix) {
          foreach ($_line->_ref_lines as $_mix_item) {
            $_view .= "$_mix_item->_ucd_view / ";
          }
        }
        else {
          $_view = $_line->_ucd_view;
        }

        $_subkey["label"] = $_view;

        $_subkey_guid = $_subkey["line"]->_guid;
        if (!isset($evenements[$key]["subitems"][$_subkey_guid])) {
          $evenements[$key]["subitems"][$_subkey_guid] = array(
            "label" => $_subkey["label"],
            "line"  => $_line,
            "items" => array(),
          );
        }

        foreach ($_line_array["administrations"] as $_adms) {
          $_adms = CModelObject::naturalSort($_adms, array("dateTime"));

          foreach ($_adms as $_adm) {
            $unite = "";
            if ($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineMix) {
              $unite = $_adm->_ref_object->_ref_produit->libelle_unite_presentation;
            }

            $evenements[$key]["subitems"][$_subkey_guid]["items"][] = array(
              "label" => "",
              "unit"  => "$_adm->quantite $unite",
              "alert" => false,
              "datetime" => $_adm->dateTime,
              "position" => CSupervisionTimedEntity::getPosition($_adm->dateTime, $time_min, $time_max),
              "object"   => $_adm,
              "editable" => false,
            );
          }
        }
      }
    }

    return $evenements;
  }
}
