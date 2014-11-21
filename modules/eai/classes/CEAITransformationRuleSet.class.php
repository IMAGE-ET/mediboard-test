<?php

/**
 * EAI transformation ruleset
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAITransformationRuleSet
 * EAI transformation ruleset
 */

class CEAITransformationRuleSet extends CMbObject {
  // DB Table key
  public $eai_transformation_ruleset_id;

  // DB fields
  public $name;
  public $description;

  // Form fields
  public $_ref_eai_transformation_rules;

  // Counts
  /** @var int */
  public $_count_transformation_rules;
  /** @var int */
  public $_count_active_transformation_rules;
  /** @var int */
  public $_count_inactive_transformation_rules;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();

    $spec->table = 'eai_transformation_ruleset';
    $spec->key   = 'eai_transformation_ruleset_id';

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["name"]        = "str notNull seekable autocomplete";
    $props["description"] = "text";

    // Derived fields
    $props["_count_transformation_rules"]          = "num";
    $props["_count_active_transformation_rules"]   = "num";
    $props["_count_inactive_transformation_rules"] = "num";

    return $props;
  }

  /**
   * @see parent::getBackProps
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["eai_transformation_rules"] = "CEAITransformationRule eai_transformation_ruleset_id";

    return $backProps;
  }

  /**
   * Load rules
   *
   * @return CEAITransformationRule[]
   */
  function loadRefsEAITransformationRules() {
    return $this->_ref_eai_transformation_rules = $this->loadBackRefs("eai_transformation_rules", "rank ASC");
  }

  /**
   * Count transformation rules
   *
   * @return int
   */
  function countRefsEAITransformationRules() {
    return $this->_count_transformation_rules = $this->countBackRefs("eai_transformation_rules");
  }

  /**
   * Count transformation rules
   *
   * @return int
   */
  function countRefsEAITransformationRulesOnlyActive() {
    return $this->_count_active_transformation_rules = $this->countBackRefs("eai_transformation_rules", array("active" => " = '1'"));
  }

  /**
   * Count transformation rules
   *
   * @return int
   */
  function countRefsEAITransformationRulesOnlyInactive() {
    return $this->_count_inactive_transformation_rules = $this->countBackRefs("eai_transformation_rules", array("active" => " = '0'"));
  }
}