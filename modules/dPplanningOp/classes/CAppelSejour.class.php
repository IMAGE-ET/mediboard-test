<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Classe CAppelSejour
 *
 * Appels de séjour
 */
class CAppelSejour extends CMbObject {
  // DB Table key
  public $appel_id;

  // DB Table key
  public $sejour_id;
  public $datetime;
  public $type;
  public $etat;
  public $commentaire;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sejour_appel';
    $spec->key   = 'appel_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"]   = "ref notNull class|CSejour";
    $props["datetime"]    = "dateTime notNull";
    $props["type"]        = "enum notNull list|admission|sortie default|admission";
    $props["etat"]        = "enum notNull list|realise|echec default|realise";
    $props["commentaire"] = "text";
    return $props;
  }
}
