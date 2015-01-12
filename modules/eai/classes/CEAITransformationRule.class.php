<?php

/**
 * EAI transformation rule
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAITransformationRule
 * EAI transformation rule
 */

class CEAITransformationRule extends CMbObject {
  // DB Table key
  public $eai_transformation_rule_id;

  // DB fields
  public $name;
  public $standard;
  public $domain;
  public $profil;
  public $message;
  public $transaction;
  public $version;
  public $extension;
  public $component_from;
  public $component_to;
  public $action_type;
  public $value;
  public $active;
  public $rank;
  public $eai_transformation_ruleset_id;

  /** @var CEAITransformationRuleSet */
  public $_ref_eai_transformation_ruleset;

  /** @var CEAITransformation[] */
  public $_ref_eai_transformations;
  public $_count_transformations;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();

    $spec->table = 'eai_transformation_rule';
    $spec->key   = 'eai_transformation_rule_id';

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["name"]           = "str notNull";
    $props["standard"]       = "str";
    $props["domain"]         = "str";
    $props["profil"]         = "str";
    $props["message"]        = "str";
    $props["transaction"]    = "str";
    $props["version"]        = "str";
    $props["extension"]      = "str";
    $props["component_from"] = "str";
    $props["component_to"]   = "str";
    $props["action_type"]    = "enum list|add|modify|move|delete";
    $props["value"]          = "str";
    $props["active"]         = "bool default|0";
    $props["rank"]           = "num min|1 show|0";

    $props["eai_transformation_ruleset_id"] = "ref class|CEAITransformationRuleSet autocomplete|text";

    // Derived fields
    $props["_count_transformations"] = "num";

    return $props;
  }

  /**
   * @see parent::getBackProps
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["eai_transformations"] = "CEAITransformation eai_transformation_rule_id";

    return $backProps;
  }

  /**
   * Load ruleset
   *
   * @return CEAITransformationRuleSet
   */
  function loadRefEAITransformationRuleSet() {
    return $this->_ref_eai_transformation_ruleset = $this->loadFwdRef("eai_transformation_ruleset_id", true);
  }

  /**
   * Load transformations
   *
   * @param array|string $order Order SQL statement
   *
   * @return CEAITransformationRule[]
   */
  function loadRefsEAITransformation($order = null) {
    return $this->_ref_eai_transformations = $this->loadBackRefs("eai_transformations", $order);
  }

  /**
   * Count transformation
   *
   * @return int
   */
  function countRefsEAITransformation() {
    return $this->_count_transformations = $this->countBackRefs("eai_transformations");
  }

  /**
   * @see parent::store
   */
  function store() {
    if (!$this->_id) {
      $transf_rule = new CEAITransformationRule();
      $transf_rule->eai_transformation_ruleset_id = $this->eai_transformation_ruleset_id;

      $this->rank = $transf_rule->countMatchingList() + 1;
    }

    return parent::store();
  }

  /**
   * Duplicate an transformation to another (or the same) category
   *
   * @param int $transformation_ruleset_dest_id RuleSet destination
   *
   * @return string
   */
  function duplicate($transformation_ruleset_dest_id) {
    $this->_id = '';

    if ($transformation_ruleset_dest_id == $this->eai_transformation_ruleset_id) {
      $this->name .= CAppUI::tr("copy_suffix");
    }
    $this->eai_transformation_ruleset_id = $transformation_ruleset_dest_id;

    return $this->store();
  }

  /**
   * Bind event
   *
   * @param CInteropNorm                                   $message Standard
   * @param CHL7Event|CHPrimXMLEvenements|CHPrimSanteEvent $event   Event
   *
   * @return bool|void
   */
  function bindObject(CInteropNorm $message, $event) {
    $where_or = array();

    if ($event instanceof CHL7Event) {
      $where_or = array(
        "standard     = '$message->name'",
        "standard     = '$message->name' AND domain = '$event->profil'",
        "profil       = '$event->profil' ",
        "transaction  = '$event->transaction'",
        "message      = '$event->code'",
      );
    }

    $where = array();
    $where[] = implode(' OR ', $where_or);

    return $where;
  }
}