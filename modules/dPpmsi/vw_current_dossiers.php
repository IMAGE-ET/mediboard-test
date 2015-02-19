<?php 

/**
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();

$date = CValue::getOrSession("date", CMbDT::date());

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date"     , $date);
$smarty->display("current_dossiers/vw_current_dossiers.tpl");