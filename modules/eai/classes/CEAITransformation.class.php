<?php

/**
 * EAI transformation
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAITransformation
 * EAI transformation
 */

class CEAITransformation extends CMbObject {
  // DB Table key
  public $eai_transformation_id;

  // DB fields
  public $actor_id;
  public $actor_class;

  public $profil;
  public $message;
  public $transaction;
  public $version;
  public $extension;

  public $active;
  public $rank;

  public $eai_transformation_rule_id;

  /** @var CEAITransformationRule */
  public $_ref_eai_transformation_rule;

  /** @var CInteropActor */
  public $_ref_actor;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();

    $spec->table = 'eai_transformation';
    $spec->key   = 'eai_transformation_id';

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["actor_id"]    = "ref notNull class|CInteropActor meta|actor_class";
    $props["actor_class"] = "str notNull class maxLength|80";

    $props["profil"]      = "str";
    $props["message"]     = "str";
    $props["transaction"] = "str";
    $props["version"]     = "str";
    $props["extension"]   = "str";

    $props["active"]      = "bool default|0";
    $props["rank"]        = "num min|1 show|0";

    $props["eai_transformation_rule_id"] = "ref class|CEAITransformationRule autocomplete|text";

    return $props;
  }

  /**
   * Load rule
   *
   * @return CEAITransformationRule
   */
  function loadRefEAITransformationRule() {
    return $this->_ref_eai_transformation_rule = $this->loadFwdRef("eai_transformation_rule_id", true);
  }

  /**
   * Load group
   *
   * @return CInteropActor
   */
  function loadRefActor() {
    return $this->_ref_actor = $this->loadFwdRef("actor_id", true);
  }
}