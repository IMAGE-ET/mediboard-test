<?php

/**
 * $Id$
 *
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CTraitementDossier extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $traitement_dossier_id;

  // DB fields
  public $traitement;
  public $validate;
  public $GHS;
  public $rss_id;
  public $sejour_id;
  public $dim_id;


  //Distant fields
  public $_ref_rss;
  public $_ref_dim;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = "traitement_dossier";
    $spec->key   = "traitement_dossier_id";
    $spec->uniques ["dossier"] = array("sejour_id", "rss_id");

    return $spec;
  }

  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["rss_id"]     = "ref class|CRSS";
    $props["sejour_id"]  = "ref class|CSejour";
    $props["traitement"] = "dateTime";
    $props["validate"]   = "dateTime";
    $props["GHS"]        = "str";
    $props["dim_id"]     = "ref class|CMediusers";

    return $props;
  }

  /**
   * Charge le DIM ayant validé le groupage.
   *
   * @return CMediusers
   */
  function loadRefDim () {
    return $this->_ref_dim = $this->loadFwdRef("dim_id");
  }
}
