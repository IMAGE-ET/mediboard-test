<?php

/**
 * dPccam
 *
 * Classe des éléments historisés de l'acte CCAM
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Class CInfoTarifCCAM
 * Table p_acte_infotarif
 *
 * Elements historisés des informations
 * Niveau acte
 */
class CInfoTarifCCAM extends CCCAM {

  public $admission_rbt;
  public $entente;
  public $date_effet;
  public $arrete_minist;
  public $publication_jo;

  public $code_exo;
  public $prescripteur;
  public $forfait;

  /**
   * Mapping des données depuis la base de données
   *
   * @param array $row Ligne d'enregistrement de de base de données
   *
   * @return void
   */
  function map($row) {
    $this->admission_rbt  = $row["REMBOURSEMENT"];
    $this->entente        = $row["ENTENTE"];
    $this->date_effet     = $row["DATEEFFET"];
    $this->arrete_minist  = $row["DATEARRETE"];
    $this->publication_jo = $row["DATEPUBLICATION"];

    $this->code_exo = array();
    $this->code_exo[1]["db"] = $row["EXOTICKET1"];
    $this->code_exo[2]["db"] = $row["EXOTICKET2"];
    $this->code_exo[3]["db"] = $row["EXOTICKET3"];
    $this->code_exo[4]["db"] = $row["EXOTICKET4"];
    $this->code_exo[5]["db"] = $row["EXOTICKET5"];

    $this->prescripteur = array();
    $this->prescripteur[1]["db"]  = $row["PRESCRIPTEUR1"];
    $this->prescripteur[2]["db"]  = $row["PRESCRIPTEUR2"];
    $this->prescripteur[3]["db"]  = $row["PRESCRIPTEUR3"];
    $this->prescripteur[4]["db"]  = $row["PRESCRIPTEUR4"];
    $this->prescripteur[5]["db"]  = $row["PRESCRIPTEUR5"];
    $this->prescripteur[6]["db"]  = $row["PRESCRIPTEUR6"];
    $this->prescripteur[7]["db"]  = $row["PRESCRIPTEUR7"];
    $this->prescripteur[8]["db"]  = $row["PRESCRIPTEUR8"];
    $this->prescripteur[9]["db"]  = $row["PRESCRIPTEUR9"];
    $this->prescripteur[10]["db"] = $row["PRESCRIPTEUR10"];

    $this->forfait = array();
    $this->forfait[1]["db"]  = $row["FORFAIT1"];
    $this->forfait[2]["db"]  = $row["FORFAIT2"];
    $this->forfait[3]["db"]  = $row["FORFAIT3"];
    $this->forfait[4]["db"]  = $row["FORFAIT4"];
    $this->forfait[5]["db"]  = $row["FORFAIT5"];
    $this->forfait[6]["db"]  = $row["FORFAIT6"];
    $this->forfait[7]["db"]  = $row["FORFAIT7"];
    $this->forfait[8]["db"]  = $row["FORFAIT8"];
    $this->forfait[9]["db"]  = $row["FORFAIT9"];
    $this->forfait[10]["db"] = $row["FORFAIT10"];
  }

  /**
   * Chargement de a liste des infos historisées pour un code
   *
   * @param string $code Code CCAM
   *
   * @return self[] Liste des info historisées
   */
  static function loadListFromCode($code) {
    $ds = self::$spec->ds;
   // mbTrace($ds);
    $query = "SELECT p_acte_infotarif.*
      FROM p_acte_infotarif
      WHERE p_acte_infotarif.CODEACTE = %
      ORDER BY p_acte_infotarif.DATEEFFET DESC";
    $query = $ds->prepare($query, $code);
    $result = $ds->exec($query);

    $listInfotarif = array();
    while ($row = $ds->fetchArray($result)) {
      $infoTarif = new CInfoTarifCCAM();
      $infoTarif->map($row);
      $listInfotarif[$row["DATEEFFET"]] = $infoTarif;
    }

    return $listInfotarif;
  }

  /**
   * Chargement des infos historisées pour un code en fonction de sa date
   *
   * @param string $code Code CCAM
   * @param string $date Date
   *
   * @return self[] Liste des info historisées pour une date donnée
   */
  static function loadFromCodeAndDate($code, $date) {
    $ds = self::$spec->ds;

    $query = "SELECT p_acte_infotarif.*
      FROM p_acte_infotarif
      WHERE p_acte_infotarif.CODEACTE = %
      AND p_acte_infotarif.DATEEFFET = $date";
    $query = $ds->prepare($query, $code);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);

    return $row;
  }

  /**
   * Chargement des libellés d'exonération
   * Table c_exotm
   *
   * @return array liste des exos
   */
  function loadLibelleExo() {
    $ds = self::$spec->ds;
    foreach ($this->code_exo as &$exo) {
      if (!$exo["db"]) {
        continue;
      }
      $query = "SELECT *
      FROM c_exotm
      WHERE c_exotm.CODE = %";
      $query = $ds->prepare($query, $exo["db"]);
      $result = $ds->exec($query);
      $row = $ds->fetchArray($result);
      $exo["libelle"] = $row["LIBELLE"];
    }
    return $this->code_exo;
  }

  /**
   * Chargement des libellés de prescripteurs
   * Table  c_categoriespecialite
   *
   * @return array liste des prescripteurs
   */
  function loadLibellePresc() {
    $ds = self::$spec->ds;
    foreach ($this->prescripteur as &$presc) {
      if (!$presc["db"]) {
        continue;
      }
      $query = "SELECT *
      FROM c_categoriespecialite
      WHERE c_categoriespecialite.CODE = %";
      $query = $ds->prepare($query, $presc["db"]);
      $result = $ds->exec($query);
      $row = $ds->fetchArray($result);
      $presc["libelle"] = $row["LIBELLE"];
    }
    return $this->prescripteur;
  }

  /**
   * Chargement des libellés des forfaits
   * Table  c_typeforfait
   *
   * @return array liste des forfaits
   */
  function loadLibelleForfait() {
    $ds = self::$spec->ds;
    foreach ($this->forfait as &$forfait) {
      if (!$forfait["db"]) {
        continue;
      }
      $query = "SELECT *
      FROM c_typeforfait
      WHERE c_typeforfait.CODE = %";
      $query = $ds->prepare($query, $forfait["db"]);
      $result = $ds->exec($query);
      $row = $ds->fetchArray($result);
      $forfait["libelle"] = $row["LIBELLE"];
    }
    return $this->forfait;
  }
}