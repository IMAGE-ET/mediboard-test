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
 * Class CConsultationCategorie
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

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'consultation_cat';
    $spec->key   = 'categorie_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["function_id"]   = "ref notNull class|CFunctions";
    $specs["nom_categorie"] = "str notNull";
    $specs["nom_icone"]     = "str notNull";
    $specs["duree"]         = "num min|1 max|15 notNull default|1 show|0";
    $specs["commentaire"]   = "text helped seekable";
    return $specs;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["consultations"] = "CConsultation categorie_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom_categorie;
  }

  function updatePlainFields() {
    parent::updatePlainFields();

    if ($this->nom_icone) {
      $this->nom_icone = basename($this->nom_icone);
    }
  }
}
