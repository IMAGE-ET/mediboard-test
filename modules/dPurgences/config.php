<?php /* $Id: config_dist.php 8507 2010-04-08 12:42:38Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8507 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
  
$dPconfig["dPurgences"] = array (
  "date_tolerance"        => "2",
  "old_rpu"               => "1",
  "rpu_warning_time"      => "00:20:00",
  "rpu_alert_time"        => "01:00:00",
  "default_view"          => "tous",
  "allow_change_patient"  => "1",
  "motif_rpu_view"        => "1",
  "age_patient_rpu_view"  => "0",
  "responsable_rpu_view"  => "1",
  "diag_prat_view"        => "0",
  "check_cotation"        => "1",
  "sortie_prevue"         => "sameday",
  "only_prat_responsable" => "0",
  "rpu_sender"            => "",
  "rpu_xml_validation"    => "1",
	"gerer_hospi"           => "1",
	"gerer_reconvoc"        => "1",
  "sibling_hours"         => "1",
);


?>