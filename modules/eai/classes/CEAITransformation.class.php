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

  public $standard;
  public $domain;
  public $profil;
  public $transaction;
  public $message;
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

    $props["standard"]    = "str";
    $props["domain"]      = "str";
    $props["profil"]      = "str";
    $props["transaction"] = "str";
    $props["message"]     = "str";
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

  /**
   * @see parent::store
   */
  function store() {
    if (!$this->_id) {
      $transformation = new CEAITransformation();
      $transformation->actor_id    = $this->actor_id;
      $transformation->actor_class = $this->actor_class;

      $this->rank = $transformation->countMatchingList() + 1;
    }

    return parent::store();
  }

  /**
   * Bind event
   *
   * @param CInteropNorm                                   $message Standard
   * @param CHL7Event|CHPrimXMLEvenements|CHPrimSanteEvent $event   Event
   * @param CInteropACtor                                  $actor   Actor
   *
   * @return bool|void
   */
  function bindObject(CInteropNorm $message, $event, CInteropActor $actor) {
    $where = array();

    $where["actor_id"]    = " = '$actor->_id'";
    $where["actor_class"] = " = '$actor->_class";

    if ($event instanceof CHL7Event) {
      $where = array(
        "profil = '$event->profil'"
      );
    }

    return $where;
  }

  /**
   * Bind transformation rule
   *
   * @param CEAITransformationRule $transformation_rule Transformation rule
   * @param CInteropACtor          $actor               Actor
   *
   * @return bool|void
   */
  function bindTransformationRule(CEAITransformationRule $transformation_rule, CInteropActor $actor) {
    $this->eai_transformation_rule_id = $transformation_rule->_id;

    $this->actor_id    = $actor->_id;
    $this->actor_class = $actor->_class;

    $this->profil      = $transformation_rule->profil;
    $this->message     = $transformation_rule->message;
    $this->transaction = $transformation_rule->transaction;
    $this->version     = $transformation_rule->version;
    $this->extension   = $transformation_rule->extension;

    $this->active      = $transformation_rule->active;
  }
}