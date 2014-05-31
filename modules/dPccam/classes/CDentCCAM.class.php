<?php

/**
 * dPccam
 *
 * Classe des dents CCAM
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Class CDentCCAM
 * Table t_localisationdents
 *
 * Dents dans la CCAM
 */
class CDentCCAM extends CCCAM {

  public $date_debut;
  public $date_fin;
  public $localisation;
  public $_libelle;

  // Utilisation du cache
  static $useCache       = true;
  static $cacheCount     = 0;
  static $useCount       = 0;

  /**
   * Mapping des données depuis la base de données
   *
   * @param array $row Ligne d'enregistrement de de base de données
   *
   * @return void
   */
  function map($row) {
    $this->date_debut   = $row["DATEDEBUT"];
    $this->date_fin     = $row["DATEFIN"];
    $this->localisation = $row["LOCDENT"];
  }

  /**
   * Chargement de a liste des dents disponibles
   *
   * @return self[] Liste des dents
   */
  static function loadList() {
    if (self::$useCache) {
      self::$useCount++;
      if ($listDents = SHM::get("dentsccam")) {
        self::$cacheCount++;
        return $listDents;
      }
    }

    $ds = self::getSpec()->ds;

    $query = "SELECT t_localisationdents.*
      FROM t_localisationdents
      ORDER BY t_localisationdents.LOCDENT ASC,
        t_localisationdents.DATEFIN ASC";
    $result = $ds->exec($query);

    $listDents = array();
    while ($row = $ds->fetchArray($result)) {
      $dent = new CDentCCAM();
      $dent->map($row);
      $dent->loadLibelle();
      $listDents[$row["DATEFIN"]][] = $dent;
    }

    if (self::$useCache) {
      SHM::put("dentsccam", $listDents);
    }

    return $listDents;
  }

  /**
   * Chargement d'une dent à partir de son numéro
   *
   * @param string $localisation Numero de la dent
   *
   * @return bool réussite du chargement
   */
  function load($localisation) {
    $ds = self::$spec->ds;
    $localisation = (int) $localisation;
    $query = "SELECT t_localisationdents.*
      FROM t_localisationdents
      WHERE t_localisationdents.LOCDENT = %";
    $query = $ds->prepare($query, $localisation);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    if (!count($row)) {
      return false;
    }
    $this->map($row);
    return true;
  }

  /**
   * Chargement du libellé de la dent
   * Table c_dentsincomp
   *
   * @return string libellé de la dent
   */
  function loadLibelle() {
    $ds = self::$spec->ds;
    $query = "SELECT *
      FROM c_dentsincomp
      WHERE c_dentsincomp.CODE = %";
      $query = $ds->prepare($query, str_pad($this->localisation, 2, "0", STR_PAD_LEFT));
      $result = $ds->exec($query);
      $row = $ds->fetchArray($result);
    $this->_libelle = $row["LIBELLE"];
    return $this->_libelle;
  }
}