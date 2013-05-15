<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Equipement de SSR, fait parti d'un plateau technique
 */
class CEquipement extends CMbObject {
  // DB Table key
  public $equipement_id;

  // References
  public $plateau_id;

  // DB Fields
  public $nom;
  public $visualisable;
  public $actif;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'equipement';
    $spec->key   = 'equipement_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["plateau_id"]   = "ref notNull class|CPlateauTechnique";
    $props["nom"]          = "str notNull";
    $props["visualisable"] = "bool notNull default|1";
    $props["actif"]        = "bool notNull default|1";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["evenements_ssr"]  = "CEvenementSSR equipement_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
}
