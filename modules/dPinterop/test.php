<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$test = new CHPrim21Reader;
$test->readFile("tmp/hprim21/adm197.hpr");
if(count($test->error_log)) {
  mbTrace($test->error_log, "Erreurs");
}

?>