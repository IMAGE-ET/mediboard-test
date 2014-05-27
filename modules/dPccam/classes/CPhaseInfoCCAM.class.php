<?php

/**
 * dPccam
 *
 * Classe des informations historisées sur les phases CCAM
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Class CPhaseInfoCCAM
 * Table p_phase_acte
 *
 * Informations historisées sur les phases
 * Niveau phase
 */
class CPhaseInfoCCAM extends CCCAM {

  public $date_effet;
  public $arrete_minist;
  public $publication_jo;
  public $nb_seances;
  public $unite_oeuvre;
  public $coeff_unite_oeuvre;
  public $code_paiement;
  public $prix_unitaire;
  public $charge_cab;
  public $coeff_dom;

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
    $this->nb_seances         = $row["NBSEANCES"];
    $this->unite_oeuvre       = $row["UNITEOEUVRE"];
    $this->coeff_unite_oeuvre = $row["COEFFUOEUVRE"];
    $this->code_paiement      = $row["CODEPAIEMENT"];
    $this->prix_unitaire      = $row["PRIXUNITAIRE"];
    $this->charge_cab         = $row["CHARGESCAB"];
    $this->coeff_dom          = array();
    $this->coeff_dom[]        = $row["COEFFDOM1"];
    $this->coeff_dom[]        = $row["COEFFDOM2"];
    $this->coeff_dom[]        = $row["COEFFDOM3"];
    $this->coeff_dom[]        = $row["COEFFDOM4"];
  }

  /**
   * Chargement de a liste des informations historisées pour une phase
   *
   * @param string $code     Code CCAM
   * @param string $activite Activité CCAM
   * @param string $phase    Phase CCAM
   *
   * @return self[] Liste des informations historisées
   */
  static function loadListFromCodeActivitePhase($code, $activite, $phase) {
    $ds = self::$spec->ds;

    $query = "SELECT p_phase_acte.*
      FROM p_phase_acte
      WHERE p_phase_acte.CODEACTE = %1
      AND p_phase_acte.ACTIVITE = %2
      AND p_phase_acte.PHASE = %3
      ORDER BY p_phase_acte.DATEEFFET DESC";
    $query = $ds->prepare($query, $code, $activite, $phase);
    $result = $ds->exec($query);

    $list_infos = array();
    while ($row = $ds->fetchArray($result)) {
      $info = new CPhaseInfoCCAM();
      $info->map($row);
      $list_infos[$row["DATEEFFET"]] = $info;
    }

    return $list_infos;
  }
}