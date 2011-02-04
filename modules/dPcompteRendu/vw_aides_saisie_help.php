<?php /* $Id: vw_idx_aides.php 9837 2010-08-18 13:42:01Z lryo $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 9837 $
* @author Thomas Despoix
*/

CCanDo::checkRead();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("vw_aides_saisie_help.tpl");
