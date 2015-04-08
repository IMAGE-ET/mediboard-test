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
$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefPatient();
$sejour->loadRefPraticien();
$sejour->loadNDA();
$sejour->loadRefTraitementDossier();

$smarty = new CSmartyDP();

$smarty->assign("_sejour" , $sejour);

$smarty->display("traitement_dossiers/inc_traitement_dossier_line.tpl");