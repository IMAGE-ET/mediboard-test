<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CRessourceSoin extends CMbObject {
  public $ressource_soin_id;

  // DB Fields
  public $cout;
  public $libelle;
  public $code;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ressource_soin';
    $spec->key   = 'ressource_soin_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["code"]    = "str notNull";
    $props["libelle"] = "str notNull";
    $props["cout"]    = "currency";
    return $props;
  }

  function updateFormFields(){
    parent::updateFormFields();

    $this->_view = $this->libelle;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["indices_couts"] = "CIndiceCout ressource_soin_id";
    return $backProps;
  }
}
