<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatient
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $can;

$ds = CSQLDataSource::get("INSEE");

if($can->read && $pays = @$_GET[$_GET["fieldpays"]]) {
  $sql = "SELECT nom_fr FROM pays
		      WHERE nom_fr LIKE '$pays%'
		      ORDER BY nom_fr";

  $result = $ds->loadList($sql, 30);
  
  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("pays"  , $pays);
  $smarty->assign("result", $result);
  $smarty->assign("nodebug", true);

  $smarty->display("httpreq_do_pays_autocomplete.tpl");
}
?>
