<?php

/**
 * dPccam
 *
 * Classe de la classification des actes CCAM
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Class CActiviteClassifCCAM
 * Table p_activite_classif
 *
 * Classification des actes
 * Niveau activite
 */
class CActiviteClassifCCAM extends CCCAM {

  public $date_effet;
  public $arrete_minist;
  public $publication_jo;
  public $categorie_medicale;
  public $_categorie_medicale;
  public $code_regroupement;
  public $_regroupement;

  /**
   * Mapping des données depuis la base de données
   *
   * @param array $row Ligne d'enregistrement de de base de données
   *
   * @return void
   */
  function map($row) {
    $this->date_effet         = $row["DATEEFFET"];
    $this->arrete_minist      = $row["DATEARRETE"];
    $this->publication_jo     = $row["DATEPUBJO"];
    $this->categorie_medicale = $row["CATMED"];
    $this->code_regroupement  = $row["REGROUP"];
  }

  /**
   * Chargement de a liste des classifications pour une activite
   *
   * @param string $code     Code CCAM
   * @param string $activite Activité CCAM
   *
   * @return self[] Liste des classifications historisées
   */
  static function loadListFromCodeActivite($code, $activite) {
    $ds = self::$spec->ds;

    $query = "SELECT p_activite_classif.*
      FROM p_activite_classif
      WHERE p_activite_classif.CODEACTE = %1
      AND p_activite_classif.ACTIVITE = %2
      ORDER BY p_activite_classif.DATEEFFET DESC";
    $query = $ds->prepare($query, $code, $activite);
    $result = $ds->exec($query);

    $list_classif = array();
    while ($row = $ds->fetchArray($result)) {
      $classif = new CActiviteClassifCCAM();
      $classif->map($row);
      $list_classif[$row["DATEEFFET"]] = $classif;
    }

    return $list_classif;
  }

  /**
   * Chargement du libellé de la catégorie médicale
   * Table c_categoriemedicale
   *
   * @return string le libellé de la catégorie
   */
  function loadCatMed() {
    $ds = self::$spec->ds;

    $query = "SELECT c_categoriemedicale.*
      FROM c_categoriemedicale
      WHERE c_categoriemedicale.CODE = %";
    $query = $ds->prepare($query, $this->categorie_medicale);
    $result = $ds->exec($query);
    if ($row = $ds->fetchArray($result)) {
      $this->_categorie_medicale = $row["LIBELLE"];
    }
    return $this->_categorie_medicale;
  }

  /**
   * Chargement du libellé de regroupement
   * Table c_coderegroupement
   *
   * @return string le libellé du regroupement
   */
  function loadRegroupement() {
    $ds = self::$spec->ds;

    $query = "SELECT c_coderegroupement.*
      FROM c_coderegroupement
      WHERE c_coderegroupement.CODE = %";
    $query = $ds->prepare($query, $this->code_regroupement);
    $result = $ds->exec($query);
    if ($row = $ds->fetchArray($result)) {
      $this->_regroupement = $row["LIBELLE"];
    }
    return $this->_regroupement;
  }
}