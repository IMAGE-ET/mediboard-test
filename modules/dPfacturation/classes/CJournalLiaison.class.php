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
 * Liaison entre les factures et leur journal
 */
class CJournalLiaison extends CMbMetaObject {

  // DB Table key
  public $journal_liaison_id;

  // DB Fields
  public $object_id;
  public $object_class;
  public $journal_id;

  // Object References
  /** @var  CFacture $_ref_facture*/
  public $_ref_facture;
  /** @var  CFacturable $_ref_facturable*/
  public $_ref_facturable;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_link_journal';
    $spec->key   = 'journal_liaison_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["object_id"]    = "ref notNull class|CFacture meta|object_class";
    $specs["object_class"] = "enum notNull list|CFactureCabinet|CFactureEtablissement show|0 default|CFactureCabinet";
    $specs["journal_id"]   = "ref notNull class|CJournalBill autocomplete|nom";
    return $specs;
  }
}