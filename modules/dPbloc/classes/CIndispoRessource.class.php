<?php

/**
 * dPbloc
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CIndispoRessource extends CMbObject {
  public $indispo_ressource_id;

  // DB References
  public $ressource_materielle_id;

  // DB Fields
  public $deb;
  public $fin;
  public $commentaire;

  /** @var CRessourceMaterielle */
  public $_ref_ressource_materielle;

  // Form Fields
  public $_debut_offset;
  public $_fin_offset;
  public $_width;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'indispo_ressource';
    $spec->key   = 'indispo_ressource_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ressource_materielle_id"] = "ref notNull class|CRessourceMaterielle autocomplete|libelle";
    $props["deb"]                     = "dateTime notNull";
    $props["fin"]                     = "dateTime notNull";
    $props["commentaire"]             = "text helped";
    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Indisponibilité du " . CMbDT::dateToLocale($this->deb);
    if ($this->deb != $this->fin) {
      $this->_view .= " au " . CMbDT::dateToLocale($this->fin);
    }
  }

  /**
   * @return CRessourceMaterielle
   */
  function loadRefRessource() {
    return $this->_ref_ressource_materielle = $this->loadFwdRef("ressource_materielle_id", true);
  }
}
