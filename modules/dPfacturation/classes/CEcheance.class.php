<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Permet d'editer des échéances pour les factures
 */
class CEcheance extends CMbMetaObject {
  // DB Table key
  public $echeance_id;

  // DB Fields
  public $object_id;
  public $object_class;
  public $date;
  public $montant;
  public $description;

  // Object References
  /** @var  CFactureCabinet|CFactureEtablissement $_ref_object*/
  public $_ref_object;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_echeance';
    $spec->key   = 'echeance_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_class"]  = "enum notNull list|CFactureCabinet|CFactureEtablissement default|CFactureCabinet";
    $props["date"]          = "date notNull";
    $props["montant"]       = "currency notNull decimals|2";
    $props["description"]   = "text";
    return $props;
  }

}