<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$ds = CSQLDataSource::get("ccamV2");

$codeacte = @$_POST["_codes_ccam"];

if ($codeacte == '') $codeacte = '%%';

CCanDo::checkRead();

$code = new CCodeCCAM(null);
$result = $code->findCodes($codeacte, $codeacte);

// Création du template
$smarty = new CSmartyDP();
$smarty->debugging = false;

$smarty->assign("_codes_ccam"  , $codeacte);
$smarty->assign("result"    , $result);
$smarty->assign("nodebug", true);

$smarty->display("httpreq_do_ccam_autocomplete.tpl");


?>