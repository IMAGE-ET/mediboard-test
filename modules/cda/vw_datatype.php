<?php 

/**
 * $Id$
 *
 * Vue des datatypes présent dans les CDA
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

//template
$smarty = new CSmartyDP();

$smarty->assign("listTypes", CCdaTools::$listDataType);

$smarty->display("vw_datatype.tpl");