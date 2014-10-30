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

    $props["name"] = "str notNull";

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
}