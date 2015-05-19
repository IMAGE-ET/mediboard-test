<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$dPconfig["dPsalleOp"] = array(
  "mode_anesth"               => "0",
  "max_add_minutes"           => "10",
  "max_sub_minutes"           => "30",
  "enable_surveillance_perop" => "0",
  "COperation"                => array(
    "mode"                   => "0",
    "modif_salle"            => "0",
    "modif_actes"            => "oneday",
    "use_entree_sortie_salle" => "1",
    "use_garrot"              => "1",
    "use_debut_fin_op"        => "1",
    "use_entree_bloc"         => "0",
    "use_remise_chir"         => "0",
    "use_suture"              => "0",
    "use_check_timing"        => "0"
  ),
  "CActeCCAM"                 => array(
    "contraste"                    => "0",
    "alerte_asso"                  => "1",
    "tarif"                        => "0",
    "restrict_display_tarif"       => "0",
    "codage_strict"                => "0",
    "check_incompatibility"        => "block",
    "signature"                    => "0",
    "openline"                     => "0",
    "modifs_compacts"              => "0",
    "commentaire"                  => "1",
    "envoi_actes_salle"            => "0",
    "envoi_motif_depassement"      => "1",
    "ext_documentaire_optionnelle" => "0",
    "del_actes_non_cotes"          => "0"
  ),
  "CDossierMedical"           => array (
    "DAS" => "0",
  ),
  "CReveil"                   => array (
    "multi_tabs_reveil" => "1",
  ),
  "CDailyCheckList"           => array(
    "active"                                        => "0",
    "active_salle_reveil"                           => "0",
    "default_good_answer_COperation"                => "0",
    "default_good_answer_CBlocOperatoire"           => "0",
    "default_good_answer_CSalle"                    => "0",
    "default_good_answer_CPoseDispositifVasculaire" => "0",
  ),
);