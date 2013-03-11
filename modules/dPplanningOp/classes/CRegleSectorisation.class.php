<?php

/**
 * look for a context to find a service
 *
 * @category DPplanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CRegleSectorisation extends CMbObject
{

  /**
   * Table Key
   *
   * @var integer
   */
  public $regle_id;

  public $service_id;
  public $function_id;
  public $praticien_id;
  public $duree_min;
  public $duree_max;
  public $date_min;
  public $date_max;
  public $type_admission;
  public $type_pec;
  public $group_id;

  //form field
  public $_ref_service;
  public $_ref_function;
  public $_ref_praticien;
  public $_ref_group;


  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec()  {
    $spec = parent::getSpec();
    $spec->table  = "regle_sectorisation";
    $spec->key    = "regle_id";
    return $spec;
  }

  /**
   * Get the properties of our class as string
   *
   * @return array
   */
  function getProps() {

    $sejour = new CSejour();
    $types_admission  = $sejour->_specs["type"]->_list;
    $types_pec        = $sejour->_specs["type_pec"]->_list;

    $props = parent::getProps();
    $props["service_id"]        = "ref class|CService seekable notNull";

    $props["function_id"]       = "ref class|CFunction";
    $props["praticien_id"]      = "ref class|CMediusers";
    $props["duree_min"]         = "num";
    $props["duree_max"]         = "num moreEquals|duree_min";
    $props["date_min"]          = "dateTime";
    $props["date_max"]          = "dateTime moreEquals|date_start";
    $props["type_admission"]    = "enum list|".implode("|", $types_admission);
    $props["type_pec"]          = "enum list|".implode("|", $types_pec);
    $props["group_id"]          = "ref class|CGroups notNull";

    return $props;
  }

  function loadRefPraticien() {
    $this->_ref_praticien = $this->loadFwdRef("praticien_id", true);
  }

  function loadRefService() {
    $this->_ref_service = $this->loadFwdRef("service_id", true);
  }

  function loadRefFunction() {
    $this->_ref_praticien = $this->loadFwdRef("function_id", true);
  }

  function loadRefGroup() {
    $this->_ref_group = $this->loadFwdRef("group_id", true);
  }
}