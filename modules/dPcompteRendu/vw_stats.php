<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: 6345 $
* @author Thomas Despoix
*/

global $m;
$m = "dPfiles";
$_GET["doc_class"] = "CCompteRendu";
CAppUI::requireModuleFile($m, "vw_stats");
$m = "dPcompteRendu";
?>