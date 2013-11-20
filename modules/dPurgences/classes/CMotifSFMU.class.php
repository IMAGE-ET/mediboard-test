<?php

/**
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Motif SFMU
 */
class CMotifSFMU extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $motif_sfmu_id;
  public $code;
  public $libelle;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "motif_sfmu";
    $spec->key    = "motif_sfmu_id";
    return $spec;  
  }
  
  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["RPU"] = "CRPU motif_sfmu";

    return $backProps;
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    $props["code"]    = "str";
    $props["libelle"] = "str";

    return $props;
  }
}
