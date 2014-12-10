<?php

/**
 * $Id$
 *  
 * @category Hospitalisation
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Description
 */
class CSousItemPrestation extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $sous_item_prestation_id;

  // DB Fields
  public $nom;
  public $item_prestation_id;
  public $niveau;

  // References
  /** @var CItemPrestation */
  public $_ref_item_prestation;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "sous_item_prestation";
    $spec->key    = "sous_item_prestation_id";
    return $spec;  
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["liaisons"] = "CItemLiaison sous_item_id";

    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["nom"] = "str";
    $props["item_prestation_id"] = "ref class|CItemPrestation";
    $props["niveau"]    = "enum list|jour|nuit";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->nom;
  }

  function loadRefItemPrestation() {
    return $this->_ref_item_prestation = $this->loadFwdRef("item_prestation_id", true);
  }
}
