<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dPconfig["ssr"] = array ( 
  "occupation_surveillance" => array (
    "faible" => "200",
    "eleve"  => "800",
  ),
  "occupation_technicien" => array (
    "faible" => "50",
    "eleve"  => "200",
  ),
  "recusation" => array (
    "sejour_readonly"        => "0",
    "use_recuse"             => "1",
    "view_services_inactifs" => "1",
  ),
	"repartition" => array (
    "show_tabs" => "1",
	),
	"CBilanSSR" => array (
    "tolerance_sejour_demandeur" => "2",
	),
  "CPrescription" => array(
    "show_dossier_soins" => "0"
  )
);