<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$ds = CSQLDataSource::get("ccamV2");

if($codeacte = @$_POST["codeacte"]){
  $sql = "SELECT CODE, LIBELLELONG FROM actes WHERE CODE LIKE '" . addslashes($codeacte) . "%' and DATEFIN = '00000000'";
}

$result = $ds->loadList($sql, null);

if ($can->read) {
  // Création du template
  $smarty = new CSmartyDP();
  $smarty->debugging = false;

  $smarty->assign("codeacte"  , $codeacte);
  $smarty->assign("result"    , $result);
  $smarty->assign("nodebug", true);

  $smarty->display("httpreq_do_ccam_autocomplete.tpl");
}

?>