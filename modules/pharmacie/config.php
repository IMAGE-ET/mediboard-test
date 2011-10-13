<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dPconfig["pharmacie"] = array (
  "dispensation_schedule" => "024",
  "show_totals_in_lists" => "1",
  "ask_stock_location_administration" => "0",
  "num_days_date_min" => 3,
  "periode_1" => array(
    "heure" => "10",
    "libelle" => "Mat",
    "couleur" => "edffd9"
   ),
  "periode_2" => array(
    "heure" => "14",
    "libelle" => "Midi",
    "couleur" => "d9ffd9"
   ),
   "periode_3" => array(
    "heure" => "18",
    "libelle" => "A.M",
    "couleur" => "d9fff6"
   ),
   "periode_4" => array(
    "heure" => "24",
    "libelle" => "Soir",
    "couleur" => "ffebd9"
   ),
   "periode_5" => array(
    "heure" => "",
    "libelle" => "",
    "couleur" => ""
   )
);