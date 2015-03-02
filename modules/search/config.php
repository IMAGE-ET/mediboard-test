<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage search
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$dPconfig["search"] = array(
  "CConfigEtab" => array(
    "active_indexing"        => "0",
    "active_handler_search"  => "0",
  ),

  "client_host"            => "",
  "client_port"            => "",
  "index_name"             => $dPconfig["db"]["std"]["dbname"],
  "nb_replicas"            => "1",
  "interval_indexing"      => "100",
);
$dPconfig["object_handlers"]["CSearchObjectHandler"] = "0";
