<?php

/**
 * Receiver HL7v2
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7Transformation
 * Transformation HL7
 */

class CHL7Transformation extends CMbObject {
  // DB Table key
  public $hl7_transformation_id;

  public $actor_id;
  public $actor_class;
  public $profil;
  public $message;
  public $version;
  public $extension;
  public $component;
  public $action;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'hl7_transformation';
    $spec->key   = 'hl7_transformation_id';

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["actor_id"]    = "ref class|CInteropActor meta|actor_class nullify";
    $props["actor_class"] = "str maxLength|80";
    $props["profil"]      = "str";
    $props["message"]     = "str";
    $props["version"]     = "str";
    $props["extension"]   = "str";
    $props["component"]   = "str";
    $props["action"]      = "enum list|add|modify|move|delete";

    return $props;
  }


}