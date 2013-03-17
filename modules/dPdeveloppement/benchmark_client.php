<?php /* $Id: benchmark.php 16260 2012-07-27 15:08:25Z lryo $ */

/**
* @package Mediboard
* @subpackage Developpement
* @version $Revision: 16260 $
* @author Romain Ollivier
*/

CCanDo::checkRead();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("benchmark_client.tpl");