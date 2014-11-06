<?php

/**
 * dPccam
 *
 * Classe des modificateurs des activités CCAM
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Class CActiviteModificateurCCAM
 * Table p_activite_modificateur
 *
 * Modificateurs disponibles pour un acte + activité
 * Niveau activite
 */
class CActiviteModificateurCCAM extends CCCAM {

  public $date_effet;
  public $date_fin;
  public $modificateur;

  public $_libelle;

  /**
   * Mapping des données depuis la base de données
   *
   * @param array $row Ligne d'enregistrement de de base de données
   *
   * @return void
   */
  function map($row) {
    $this->date_effet   = $row["DATEEFFET"];
    $this->date_fin     = $row["DATEFIN"];
    $this->modificateur = $row["MODIFICATEUR"];
  }

  /**
   * Retourne la liste des dates d'effet disponible pour les modificateurs d'un acte
   *
   * @param string $code Code CCAM
   *
   * @return array
   */
  static function loadDateEffetList($code) {
    $ds = self::$spec->ds;

    $query = "SELECT DATEEFFET
      FROM p_activite_modificateur
      WHERE p_activite_modificateur.CODEACTE = %1
      GROUP BY p_activite_modificateur.DATEEFFET
      ORDER BY p_activite_modificateur.DATEEFFET DESC";
    $query = $ds->prepare($query, $code);
    $result = $ds->exec($query);
    $listDates = array();
    while ($row = $ds->fetchArray($result)) {
      $listDates[] = $row["DATEEFFET"];
    }

    return $listDates;
  }

  /**
   * Chargement de a liste des modificateurs pour une activité
   *
   * @param string $code     Code CCAM
   * @param string $activite Activité CCAM
   *
   * @return self[][] Liste des modificateurs
   */
  static function loadListFromCodeActivite($code, $activite) {
    $ds = self::$spec->ds;

    $query = "SELECT p_activite_modificateur.*, t_modificateurinfooc.DATEFIN
      FROM p_activite_modificateur
      LEFT JOIN t_modificateurinfooc ON t_modificateurinfooc.CODE = p_activite_modificateur.MODIFICATEUR
      WHERE p_activite_modificateur.CODEACTE = %1
      AND p_activite_modificateur.CODEACTIVITE = %2
      ORDER BY p_activite_modificateur.DATEEFFET DESC, p_activite_modificateur.MODIFICATEUR";
    $query = $ds->prepare($query, $code, $activite);
    $result = $ds->exec($query);

    $list_modifs = array();
    $listDatesEffet = self::loadDateEffetList($code);

    foreach ($listDatesEffet as $date) {
      $list_modifs[$date] = array();
    }
    while ($row = $ds->fetchArray($result)) {
      $modif = new CActiviteModificateurCCAM();
      $modif->map($row);
      $list_modifs[$row["DATEEFFET"]][] = $modif;
    }

    return $list_modifs;
  }

  /**
   * Chargement du libellé du modificateur
   * Table l_modificateur
   *
   * @return string Le libellé du modificateur
   */
  function loadLibelle() {

    $ds = self::$spec->ds;

    $query = "SELECT l_modificateur.*
      FROM l_modificateur
      WHERE l_modificateur.CODE = %";
    $query = $ds->prepare($query, $this->modificateur);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    return $this->_libelle = $row["LIBELLE"];
  }
}