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
    "use_auto_cloture"    => "0",
    "view_bill"           => "1"
  ),
  "CFactureCabinet" => array(
    "use_auto_cloture"  => "1",
    "view_bill"         => "1"
  ),
  "CRelance" => array(
    "use_relances"   => "0",
    "nb_days_first_relance"   => "30",
    "nb_days_second_relance"  => "60",
    "nb_days_third_relance"   => "90",
    "add_first_relance"       => "0",
    "add_second_relance"      => "0",
    "add_third_relance"       => "0",
    "message1_relance"        => "",
    "message2_relance"        => ""
  ),
  "CReglement" => array(
    "use_debiteur" => "0",
    "add_pay_not_close"     => "0",
    "use_lock_acquittement" => "0",
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
    "use_view_chainage"     => "0",
    "use_view_quantitynull" => "0",
    "use_strict_cloture"    => "0",
    "use_field_definitive"  => "0"
  ),
);
?>