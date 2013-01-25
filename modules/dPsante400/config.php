<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dPconfig["dPsante400"] = array (
  "nb_rows"     => "5",
  "mark_row"    => "0",
  "cache_hours" => "1",
  "dsn"         => "",
  "user"        => "",
  "pass"        => "",
  "group_id"    => "",
  "CSejour"     => array(
    "sibling_hours" => 1,
  ),
  "CIncrementer" => array(
    "cluster_count"    => 1,
    "cluster_position" => 0,
  ),
);
