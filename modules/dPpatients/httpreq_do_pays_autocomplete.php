<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatient
* @version $Revision: $
* @author Sébastien Fillonneau
*/
global $can;

$ds = CSQLDataSource::get("INSEE");
$query = null;

if ($pays = @$_GET[$_GET["fieldpays"]]) {
  $query = "SELECT numerique, nom_fr FROM pays
            WHERE nom_fr LIKE '$pays%'
            ORDER BY nom_fr, numerique";
}

if ($numPays = @$_GET[$_GET["fieldnumericpays"]]) {
  $query = "SELECT numerique, nom_fr FROM pays
            WHERE numerique LIKE '%$numPays%'
            ORDER BY nom_fr, numerique";
}

if ($can->read && $query) {
  $result = $ds->loadList($query, 30);
  
  // Création du template
  $smarty = new CSmartyDP();
  
  $smarty->assign("pays"    , $pays);
  $smarty->assign("numPays" , $numPays);
  $smarty->assign("result"  , $result);
  $smarty->assign("nodebug", true);
  
  $smarty->display("httpreq_do_pays_autocomplete.tpl");
}


?>