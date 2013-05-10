<?php /* $Id: acte.class.php 8867 2010-05-07 07:21:19Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision: 8867 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CFraisDivers extends CActe {
  public $frais_divers_id;

  // DB fields
  public $type_id;
  public $coefficient;
  public $quantite;

  public $_montant;

  public $_ref_type;

  function getProps() {
    $props = parent::getProps();
    $props["type_id"]     = "ref notNull class|CFraisDiversType autocomplete|code";
    $props["coefficient"] = "float notNull default|1";
    $props["quantite"]    = "num min|0";

    $props["_montant"]    = "currency";
    return $props;
  }

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "frais_divers";
    $spec->key   = "frais_divers_id";
    return $spec;
  }

  function loadRefType(){
    return $this->_ref_type = $this->loadFwdRef("type_id", true);
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefType();

    $this->_montant = $this->montant_base;

    // Vue codée
    $this->_shortview  = $this->quantite > 1 ? "{$this->quantite}x " : "";
    $this->_shortview .= $this->_ref_type->_view;

    if ($this->coefficient != 1) {
      $this->_shortview .= $this->coefficient;      
    }

    $this->_view = "Frais divers $this->_shortview";
    if ($this->object_class && $this->object_id) {
      $this->_view .= " de $this->object_class-$this->object_id";
    }
  }
}
