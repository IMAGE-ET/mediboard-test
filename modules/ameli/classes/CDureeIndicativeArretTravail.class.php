<?php

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */
 
/**
 * Description
 */
class CDureeIndicativeArretTravail {
  public $duree_indicative_id;
  public $code_motif;
  public $_depth;
  public $_nb_duration;
  /** @var CCritereDureeArretTravail[] */
  public $criteres = array();

  /**
   * The constructor
   *
   * @param array $fields The fields
   */
  function __construct($fields) {
    $this->duree_indicative_id = $fields['duree_indicative_id'];
    $this->code_motif = $fields['code_motif'];
  }

  /**
   * Load the criteria for the motif
   *
   * @return void
   */
  function loadCriteres() {
    if (!is_null($this->duree_indicative_id)) {
      $criteres = CCritereDureeArretTravail::loadCriteresByParent($this->duree_indicative_id, 'DureeIndicative');
      foreach ($criteres as $_critere) {
        $_critere->loadChildren();
      }

      $this->criteres = $criteres;
    }
  }

  /**
   * Compute the depth of the criteria
   *
   * @return void
   */
  function getDepth() {
    $max_depth = 0;
    $max_nb_duration = 0;
    foreach ($this->criteres as $_critere) {
      $_critere->getDepth();
      if ($_critere->_depth > $max_depth) {
        $max_depth = $_critere->_depth;
      }
      if ($_critere->_nb_duration > $max_nb_duration) {
        $max_nb_duration = $_critere->_nb_duration;
      }
    }
    $this->_depth += $max_depth + 1;
    $this->_nb_duration = $max_nb_duration;
  }

  /**
   * Get the Duree indicative for a motif
   *
   * @param string $code_motif The code of the motif
   *
   * @return CDureeIndicativeArretTravail|null
   */
  static function loadForMotif($code_motif) {
    $ds = CSQLDataSource::get('ameli', true);
    if (!$ds) {
      return null;
    }

    $query = "SELECT * FROM `duree_indicative_aati` WHERE `code_motif` = '$code_motif';";
    $result = $ds->exec($query);
    if (!$result) {
      return null;
    }
    $row = $ds->fetchAssoc($result);

    if (!$row) {
      return null;
    }

    return new CDureeIndicativeArretTravail($row);
  }
}
