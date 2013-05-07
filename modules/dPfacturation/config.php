<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

$dPconfig["dPfacturation"] = array (
  "CFactureEtablissement" => array(
    "use_temporary_bill"  => "0",
    "use_auto_cloture"    => "0"
  ),
  "CFactureCabinet" => array(
    "use_auto_cloture"  => "1"
  ),
  "CRelance" => array(
    "use_relances"   => "0",
    "nb_days_first_relance"   => "30",
    "nb_days_second_relance"  => "60"
  ),
  "CRetrocession" => array(
    "use_retrocessions" => "0"
  ),
  "CEditPdf" => array(
    "use_bill_etab" => "0",
    "home_nom"      => "",
    "home_adresse"  => "",
    "home_cp"       => "",
    "home_ville"    => "",
    "home_EAN"      => "",
    "home_RCC"      => "",
    "home_tel"      => "",
    "home_fax"      => ""
  ),
  "Other" => array(
    "use_view_chainage" => "0"
  ),
);
?>