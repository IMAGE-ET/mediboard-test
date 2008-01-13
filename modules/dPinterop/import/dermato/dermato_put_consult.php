<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$limitConsult = mbGetValueFromGetOrSession("limitConsult", 0);
$ds = CSQLDataSource::get("std");
if ($limitConsult == -1) {
  return;	
}

$can->needsRead();

set_time_limit( 1800 );
?>