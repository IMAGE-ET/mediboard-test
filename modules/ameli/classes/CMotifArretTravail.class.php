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
class CMotifArretTravail {

  /** @var string */
  public $code;
  /** @var string  */
  public $libelle;
  /** @var string 'groupe' or 'motif' */
  public $type;
  /** @var boolean */
  public $complement;
  /** @var string  */
  public $codification;
  /** @var string  */
  public $correspondance;
  /** @var CDureeIndicativeArretTravail[]  */
  public $ref_duree_indicative;
  /** @var CMotifArretTravail[]  */
  public $_ref_children;
  /** @var CMotifArretTravail  */
  public $_ref_group;

  /**
   * The constructor
   *
   * @param array $fields The fields
   */
  public function __construct($fields = array()) {
    if (array_key_exists('code', $fields)) {
      $this->code = $fields['code'];
    }
    if (array_key_exists('libelle', $fields)) {
      $this->libelle = $fields['libelle'];
    }
    if (array_key_exists('type', $fields)) {
      $this->type = $fields['type'];
    }
    if (array_key_exists('complement', $fields)) {
      $this->complement = $fields['complement'];
    }
    if (array_key_exists('codification', $fields)) {
      $this->codification = $fields['codification'];
    }
    if (array_key_exists('correspondance', $fields)) {
      $this->correspondance = $fields['correspondance'];
    }
  }

  /**
   * Load the indicative duration for this motif
   *
   * @return void
   */
  public function loadDureeIndicative() {
    $duree = CDureeIndicativeArretTravail::loadForMotif($this->code);

    $duree->loadCriteres();
    $this->ref_duree_indicative = $duree;
  }

  /**
   * If the motif is a group, load it's children
   *
   * @return void
   */
  public function loadChildren() {
    if ($this->type != 'groupe') {
      return;
    }

    $this->_ref_children = CMotifArretTravail::searchMotifsByCode(substr($this->code, 0, 4), 'motif');
  }

  /**
   * If the motif is a motif, load it's group
   *
   * @return void
   */
  public function loadGroup() {
    if ($this->type != 'motif') {
      return;
    }

    $this->_ref_group = CMotifArretTravail::searchByCode(substr($this->code, 0, 4) . '0000', 'groupe');
  }

  /**
   * Search a motif by using the code
   *
   * @param string $code The code of the motif
   * @param string $type The type of the motif (groupe or motif)
   *
   * @return CMotifArretTravail|null
   */
  public static function searchByCode($code, $type = null) {
    $ds = CSQLDataSource::get('ameli', true);
    if (!$ds) {
      return null;
    }

    $query = "SELECT * FROM `motif_aati` WHERE `code` LIKE '$code'";
    if (!is_null($type)) {
      $query .= " AND `type` = '$type'";
    }

    $result = $ds->exec($query . ';');
    if (!$result) {
      return null;
    }
    $row = $ds->fetchAssoc($result);

    if (!$row) {
      return null;
    }

    return new CMotifArretTravail($row);
  }

  /**
   * Search motifs by using the code
   *
   * @param string $code The code
   * @param string $type The type of the motif (groupe or motif)
   *
   * @return CMotifArretTravail[]
   */
  public static function searchMotifsByCode($code, $type = '') {
    $ds = CSQLDataSource::get('ameli', true);
    $motifs = array();

    if (!$ds) {
      return $motifs;
    }

    $query = "SELECT * FROM `motif_aati` WHERE `code` LIKE '$code%'";
    if (!is_null($type)) {
      $query .= " AND `type` = '$type'";
    }

    $result = $ds->exec($query . ';');
    if ($result) {
      while ($row = $ds->fetchAssoc($result)) {
        $motifs[] = new CMotifArretTravail($row);
      }
    }

    return $motifs;
  }

  /**
   * Search motifs by using the libelle
   *
   * @param string $libelle The libelle
   *
   * @return CMotifArretTravail[]
   */
  public static function searchMotifsByLibelle($libelle) {
    $ds = CSQLDataSource::get('ameli', true);
    $motifs = array();
    if (!$ds) {
      return $motifs;
    }

    $query = "SELECT * FROM `motif_aati` WHERE `libelle` LIKE '%$libelle%'";
    $result = $ds->exec($query);
    if ($result) {
      while ($row = $ds->fetchAssoc($result)) {
        $motifs[] = new CMotifArretTravail($row);
      }
    }

    return $motifs;
  }
}
