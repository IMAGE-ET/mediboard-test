<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Items de prestation
 */
class CItemPrestation extends CMbMetaObject{
  // DB Table key
  public $item_prestation_id;
  
  // DB Fields
  public $nom;
  public $rank;
  
  // Form field
  public $_quantite;

  /** @var CPrestationPonctuelle|CPrestationJournaliere */
  public $_ref_object;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "item_prestation";
    $spec->key   = "item_prestation_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]          = "str notNull seekable";
    /*$specs["object_id"]    = "ref notNull class|CMbObject meta|object_class";*/
    $specs["object_class"] = "enum list|CPrestationPonctuelle|CPrestationJournaliere";
    $specs["rank"]         = "num pos default|1";
    return $specs;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["liaisons_souhaits"] = "CItemLiaison item_souhait_id";
    $backProps["liaisons_realises"] = "CItemLiaison item_realise_id";
    $backProps["liaisons_lits"]     = "CLitLiaisonItem item_prestation_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

  /**
   * Charge la prestation
   *
   * @return CPrestationPonctuelle|CPrestationJournaliere
   */
  function loadRefObject(){
    $this->_ref_object = new $this->object_class;
    return $this->_ref_object = $this->_ref_object->getCached($this->object_id);
  }
}