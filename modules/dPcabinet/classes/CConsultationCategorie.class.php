<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Catégorie de cosultation, couleurs et icones pour mieux les classer
 */
class CConsultationCategorie extends CMbObject {
  public $categorie_id;

  // DB References
  public $function_id;

  // DB fields
  public $nom_categorie;
  public $nom_icone;
  public $duree;
  public $commentaire;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'consultation_cat';
    $spec->key   = 'categorie_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["function_id"]   = "ref notNull class|CFunctions";
    $props["nom_categorie"] = "str notNull";
    $props["nom_icone"]     = "str notNull";
    $props["duree"]         = "num min|1 max|15 notNull default|1 show|0";
    $props["commentaire"]   = "text helped seekable";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["consultations"] = "CConsultation categorie_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom_categorie;
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    if ($this->nom_icone) {
      $this->nom_icone = basename($this->nom_icone);
    }
  }
}
